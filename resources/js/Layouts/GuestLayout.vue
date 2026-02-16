<script setup>
import PortalLogo from "@/Components/PortalLogo.vue";
import GoogleTranslate from "@/Components/GoogleTranslate.vue";
import { Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const page = usePage();

const currentUrl = computed(() =>
    typeof page.url === "string" ? page.url : "",
);

const isFullWidthPage = computed(
    () =>
        currentUrl.value.startsWith("/privacy-policy") ||
        currentUrl.value.startsWith("/terms-and-conditions"),
);

const scene = computed(() => {
    if (currentUrl.value.includes("/register")) {
        return {
            badge: "Admissions Flow",
            title: "Create Your Academic Portal Account",
            description:
                "Set up one secure account for enrollment, timetable access, grades, fee tracking, and campus updates.",
            subtitle: "Fast onboarding for students and staff",
        };
    }

    if (currentUrl.value.includes("/forgot-password")) {
        return {
            badge: "Account Recovery",
            title: "Reset Access Safely",
            description:
                "Recover your account in minutes with secure password reset and verification steps.",
            subtitle: "Your account protection comes first",
        };
    }

    if (currentUrl.value.includes("/reset-password")) {
        return {
            badge: "Security Update",
            title: "Set a New Password",
            description:
                "Use a strong password to keep your profile, grades, fees, and messages protected.",
            subtitle: "Built for secure campus operations",
        };
    }

    if (currentUrl.value.includes("/verify-email")) {
        return {
            badge: "Email Verification",
            title: "Activate Your Account",
            description:
                "Confirm your email to unlock full portal access and receive official academic notifications.",
            subtitle: "Reliable communication and account trust",
        };
    }

    if (currentUrl.value.includes("/confirm-password")) {
        return {
            badge: "Protected Action",
            title: "Confirm Before Proceeding",
            description:
                "For sensitive actions, we ask for password confirmation to keep your account secure.",
            subtitle: "Security checks for critical actions",
        };
    }

    return {
        badge: "Student and Staff Access",
        title: "Welcome Back to the Academic Portal",
        description:
            "Sign in to manage courses, attendance, assessments, announcements, and support requests from one place.",
        subtitle: "A connected experience across your academic journey",
    };
});

const highlightItems = [
    "Enrollment and timetable access in one dashboard",
    "Real-time grade, fee, and attendance visibility",
    "Secure communication and multilingual support",
];

const quickStats = [
    { label: "Courses", value: "100+" },
    { label: "Students", value: "5K+" },
    { label: "Support", value: "24/7" },
];
</script>

<template>
    <div class="guest-shell relative isolate flex min-h-screen flex-col overflow-hidden">
        <div aria-hidden="true" class="guest-ambient">
            <span class="guest-orb guest-orb--a"></span>
            <span class="guest-orb guest-orb--b"></span>
            <span class="guest-orb guest-orb--c"></span>
            <span class="guest-grid"></span>
        </div>

        <div class="fixed right-4 top-4 z-50">
            <GoogleTranslate />
        </div>

        <template v-if="isFullWidthPage">
            <header class="relative z-10 border-b border-white/15 bg-slate-950/35 backdrop-blur-xl">
                <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                    <Link
                        href="/"
                        class="inline-flex items-center gap-3 text-slate-100 transition-colors hover:text-portal-gold"
                    >
                        <PortalLogo class="h-10 w-10 shrink-0" />
                        <span class="guest-brand text-sm uppercase tracking-[0.2em]">
                            University Academic Portal
                        </span>
                    </Link>
                    <div class="flex items-center gap-3">
                        <Link
                            :href="route('privacy-policy')"
                            class="rounded-full border border-white/20 bg-white/5 px-3 py-1.5 text-xs font-semibold text-slate-200 transition hover:bg-white/10"
                        >
                            Privacy Policy
                        </Link>
                        <Link
                            :href="route('terms-and-conditions')"
                            class="rounded-full border border-white/20 bg-white/5 px-3 py-1.5 text-xs font-semibold text-slate-200 transition hover:bg-white/10"
                        >
                            Terms and Conditions
                        </Link>
                    </div>
                </div>
            </header>

            <main class="relative z-10 min-h-0 w-full flex-1">
                <slot />
            </main>

            <footer class="relative z-10 border-t border-white/10 px-4 py-3 text-center text-xs text-slate-300/80">
                BSc (Hons) Computing - University Academic Portal Project
            </footer>
        </template>

        <template v-else>
            <main class="relative z-10 flex flex-1 items-stretch">
                <div class="mx-auto flex w-full max-w-6xl flex-col gap-6 px-4 pb-8 pt-7 sm:px-6 sm:pb-10 lg:flex-row lg:items-center lg:gap-8 lg:px-8">
                    <section
                        class="guest-hero-panel w-full rounded-3xl border border-white/20 bg-slate-950/35 p-6 text-slate-100 shadow-2xl backdrop-blur-xl lg:w-1/2 lg:p-8"
                    >
                        <Link
                            href="/"
                            class="portal-logo-link mb-6 inline-flex items-center gap-3 rounded-full border border-white/20 bg-white/10 px-4 py-2 transition hover:bg-white/20"
                        >
                            <PortalLogo class="h-10 w-10 shrink-0" />
                            <span class="guest-brand text-xs uppercase tracking-[0.2em]">
                                University Academic Portal
                            </span>
                        </Link>

                        <p class="guest-badge inline-flex items-center rounded-full border border-amber-300/40 bg-amber-200/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.14em] text-amber-200">
                            {{ scene.badge }}
                        </p>

                        <h1 class="guest-title mt-4 text-3xl font-semibold leading-tight sm:text-4xl">
                            {{ scene.title }}
                        </h1>

                        <p class="mt-3 text-sm text-slate-200/90 sm:text-base">
                            {{ scene.description }}
                        </p>
                        <p class="mt-2 text-xs font-semibold uppercase tracking-[0.12em] text-slate-300/80">
                            {{ scene.subtitle }}
                        </p>

                        <ul class="mt-6 space-y-2 text-sm text-slate-200/90">
                            <li
                                v-for="item in highlightItems"
                                :key="item"
                                class="flex items-start gap-2"
                            >
                                <span class="mt-1.5 h-1.5 w-1.5 rounded-full bg-portal-gold"></span>
                                <span>{{ item }}</span>
                            </li>
                        </ul>

                        <div class="mt-7 grid grid-cols-3 gap-3">
                            <div
                                v-for="metric in quickStats"
                                :key="metric.label"
                                class="rounded-2xl border border-white/15 bg-white/5 px-3 py-3 text-center"
                            >
                                <p class="text-lg font-semibold text-amber-200">{{ metric.value }}</p>
                                <p class="text-[11px] uppercase tracking-[0.1em] text-slate-300">{{ metric.label }}</p>
                            </div>
                        </div>
                    </section>

                    <section class="w-full lg:w-1/2">
                        <div class="guest-form-shell overflow-hidden rounded-3xl border border-slate-200/90 bg-white/95 p-6 shadow-2xl ring-1 ring-slate-100 backdrop-blur-md sm:p-8">
                            <slot />
                        </div>

                        <div class="mt-4 flex flex-wrap items-center justify-center gap-3 text-xs text-slate-100/90">
                            <Link
                                :href="route('privacy-policy')"
                                class="rounded-full border border-white/25 bg-white/10 px-3 py-1.5 font-semibold transition hover:bg-white/20"
                            >
                                Privacy Policy
                            </Link>
                            <Link
                                :href="route('terms-and-conditions')"
                                class="rounded-full border border-white/25 bg-white/10 px-3 py-1.5 font-semibold transition hover:bg-white/20"
                            >
                                Terms and Conditions
                            </Link>
                        </div>
                    </section>
                </div>
            </main>

            <footer class="relative z-10 px-4 pb-5 text-center text-xs text-slate-200/80">
                BSc (Hons) Computing - University Academic Portal Project
            </footer>
        </template>
    </div>
</template>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Source+Sans+3:wght@400;500;600;700&display=swap");

.guest-shell {
    font-family: "Source Sans 3", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    background:
        radial-gradient(70rem 70rem at -18% -18%, rgba(44, 114, 176, 0.26), transparent 54%),
        radial-gradient(64rem 64rem at 112% -20%, rgba(244, 180, 0, 0.2), transparent 50%),
        linear-gradient(145deg, #091a35 0%, #102a4f 32%, #0f253e 100%);
}

.guest-ambient {
    pointer-events: none;
    position: absolute;
    inset: 0;
    z-index: 0;
    overflow: hidden;
}

.guest-orb {
    position: absolute;
    border-radius: 999px;
    filter: blur(68px);
    opacity: 0.42;
    animation: drift 15s ease-in-out infinite;
}

.guest-orb--a {
    left: -7rem;
    top: -4rem;
    height: 21rem;
    width: 21rem;
    background: rgba(31, 122, 140, 0.36);
}

.guest-orb--b {
    right: -7rem;
    top: 10rem;
    height: 19rem;
    width: 19rem;
    background: rgba(244, 180, 0, 0.34);
    animation-delay: 2.5s;
}

.guest-orb--c {
    left: 40%;
    bottom: -8rem;
    height: 20rem;
    width: 20rem;
    background: rgba(59, 130, 246, 0.3);
    animation-delay: 4.5s;
}

.guest-grid {
    position: absolute;
    inset: 0;
    opacity: 0.2;
    background-image:
        linear-gradient(rgba(255, 255, 255, 0.12) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255, 255, 255, 0.12) 1px, transparent 1px);
    background-size: 34px 34px;
    mask-image: radial-gradient(circle at center, rgba(0, 0, 0, 0.72), transparent 70%);
    -webkit-mask-image: radial-gradient(circle at center, rgba(0, 0, 0, 0.72), transparent 70%);
}

.guest-title,
.guest-brand,
.guest-badge {
    font-family: "Space Grotesk", "Source Sans 3", sans-serif;
}

.guest-form-shell {
    animation: riseIn 0.5s ease-out;
}

.guest-hero-panel {
    animation: riseIn 0.55s ease-out;
}

.guest-form-shell :deep(label) {
    color: #0f172a;
    font-weight: 600;
}

.guest-form-shell :deep(input),
.guest-form-shell :deep(select),
.guest-form-shell :deep(textarea) {
    border-radius: 0.8rem;
    border-color: #cbd5e1;
    box-shadow: none;
    transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
}

.guest-form-shell :deep(input:focus),
.guest-form-shell :deep(select:focus),
.guest-form-shell :deep(textarea:focus) {
    border-color: rgba(11, 31, 58, 0.65);
    box-shadow: 0 0 0 3px rgba(11, 31, 58, 0.16);
}

.guest-form-shell :deep(button[type="submit"]),
.guest-form-shell :deep(a[class*="bg-"]),
.guest-form-shell :deep(button[class*="bg-"]) {
    border-radius: 999px;
}

@keyframes drift {
    0%, 100% {
        transform: translateY(0) scale(1);
    }
    50% {
        transform: translateY(-18px) scale(1.04);
    }
}

@keyframes riseIn {
    from {
        opacity: 0;
        transform: translateY(14px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 1024px) {
    .guest-grid {
        opacity: 0.15;
    }
}

@media (max-width: 640px) {
    .guest-orb {
        filter: blur(50px);
        opacity: 0.33;
    }
}

@media (prefers-reduced-motion: reduce) {
    .guest-orb,
    .guest-form-shell,
    .guest-hero-panel {
        animation: none !important;
    }
}
</style>