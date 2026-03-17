import { computed, onMounted, onUnmounted, ref } from "vue";
import { router } from "@inertiajs/vue3";

const UNREAD_REFRESH_INTERVAL_MS = 20000;

export function useAuthenticatedLayoutNotifications(page) {
    const unreadNotificationCount = computed(() =>
        Number(page.props.unread?.notifications ?? 0)
    );
    const notificationsPreview = computed(
        () => page.props.notificationsPreview?.items ?? []
    );
    const isMarkingNotificationsRead = ref(false);
    const isRefreshingUnreadSharedProps = ref(false);

    let stopRouterSuccessListener = null;
    let unreadRefreshTimer = null;

    const refreshUnreadSharedProps = () => {
        if (isRefreshingUnreadSharedProps.value) {
            return;
        }

        isRefreshingUnreadSharedProps.value = true;

        router.reload({
            only: ["unread", "notificationsPreview"],
            preserveState: true,
            preserveScroll: true,
            onFinish: () => {
                isRefreshingUnreadSharedProps.value = false;
            },
        });
    };

    const startUnreadRefreshTimer = () => {
        if (unreadRefreshTimer !== null || typeof window === "undefined") {
            return;
        }

        unreadRefreshTimer = window.setInterval(() => {
            if (typeof document !== "undefined" && document.hidden) {
                return;
            }

            refreshUnreadSharedProps();
        }, UNREAD_REFRESH_INTERVAL_MS);
    };

    const stopUnreadRefreshTimer = () => {
        if (unreadRefreshTimer === null || typeof window === "undefined") {
            return;
        }

        window.clearInterval(unreadRefreshTimer);
        unreadRefreshTimer = null;
    };

    const handleVisibilityChange = () => {
        if (typeof document !== "undefined" && document.hidden) {
            return;
        }

        refreshUnreadSharedProps();
    };

    const markAllNotificationsRead = () => {
        if (
            isMarkingNotificationsRead.value ||
            unreadNotificationCount.value <= 0
        ) {
            return;
        }

        isMarkingNotificationsRead.value = true;

        router.post(
            route("notifications.read-all"),
            {},
            {
                preserveScroll: true,
                preserveState: true,
                replace: true,
                onFinish: () => {
                    isMarkingNotificationsRead.value = false;
                },
            }
        );
    };

    const openNotificationFromPreview = (notification) => {
        const targetUrl = notification?.url || route("notifications.index");
        const notificationId = notification?.id;

        if (!notificationId || notification?.read_at) {
            router.visit(targetUrl);
            return;
        }

        router.post(
            route("notifications.read", notificationId),
            {},
            {
                preserveScroll: true,
                preserveState: true,
                replace: true,
                onSuccess: () => {
                    router.visit(targetUrl);
                },
                onError: () => {
                    router.visit(targetUrl);
                },
            }
        );
    };

    const openNotificationCenter = () => {
        router.visit(route("notifications.index"));
    };

    const truncateNotificationText = (value, max = 96) => {
        const text = String(value ?? "").trim();
        if (!text) {
            return "No details available.";
        }

        return text.length > max ? `${text.slice(0, max)}...` : text;
    };

    onMounted(() => {
        stopRouterSuccessListener = router.on("success", (event) => {
            const method = String(event?.detail?.visit?.method ?? "get")
                .trim()
                .toLowerCase();
            const visitOnly = Array.isArray(event?.detail?.visit?.only)
                ? event.detail.visit.only
                : [];
            const updatesUnreadViaPartialReload = visitOnly.some(
                (key) =>
                    key === "unread" ||
                    key.startsWith("unread.") ||
                    key === "notificationsPreview" ||
                    key.startsWith("notificationsPreview.")
            );

            if (
                method === "get" &&
                (visitOnly.length === 0 || updatesUnreadViaPartialReload)
            ) {
                return;
            }

            refreshUnreadSharedProps();
        });

        startUnreadRefreshTimer();

        if (typeof document !== "undefined") {
            document.addEventListener(
                "visibilitychange",
                handleVisibilityChange
            );
        }
    });

    onUnmounted(() => {
        if (typeof stopRouterSuccessListener === "function") {
            stopRouterSuccessListener();
        }

        stopUnreadRefreshTimer();

        if (typeof document !== "undefined") {
            document.removeEventListener(
                "visibilitychange",
                handleVisibilityChange
            );
        }
    });

    return {
        unreadNotificationCount,
        notificationsPreview,
        isMarkingNotificationsRead,
        markAllNotificationsRead,
        openNotificationFromPreview,
        openNotificationCenter,
        truncateNotificationText,
    };
}
