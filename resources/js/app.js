import "../css/app.css";
import "./bootstrap";

import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createApp, h } from "vue";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";

const configuredAppName =
    typeof document !== "undefined"
        ? document
              .querySelector('meta[name="application-name"]')
              ?.getAttribute("content")
        : null;

// Default application name shown in browser titles
const appName =
    configuredAppName ||
    import.meta.env.VITE_APP_NAME ||
    "University Academic Portal";
const disableNativeValidation =
    import.meta.env.DEV ||
    String(import.meta.env.VITE_DISABLE_HTML5_VALIDATION || "").toLowerCase() ===
        "true";

const applyNoValidateToForms = (root) => {
    if (!root) return;

    if (root instanceof HTMLFormElement) {
        root.setAttribute("novalidate", "novalidate");
        return;
    }

    if (typeof root.querySelectorAll === "function") {
        root.querySelectorAll("form").forEach((form) => {
            form.setAttribute("novalidate", "novalidate");
        });
    }
};

const enableBackendValidationTestingMode = () => {
    applyNoValidateToForms(document);

    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node) => {
                if (node instanceof Element) {
                    applyNoValidateToForms(node);
                }
            });
        });
    });

    observer.observe(document.body, { childList: true, subtree: true });
};

const inertiaRoot = document.getElementById("app");
const hasInertiaPage = inertiaRoot?.dataset?.page;

if (inertiaRoot && hasInertiaPage) {
    createInertiaApp({
        title: (title) => `${title} - ${appName}`,
        resolve: (name) =>
            resolvePageComponent(
                `./Pages/${name}.vue`,
                import.meta.glob("./Pages/**/*.vue")
            ),
        setup({ el, App, props, plugin }) {
            return createApp({ render: () => h(App, props) })
                .use(plugin)
                .use(ZiggyVue)
                .mount(el);
        },
        progress: {
            color: "#0f172a", // dark academic navy
        },
    });
}

if (disableNativeValidation) {
    if (document.readyState === "loading") {
        document.addEventListener(
            "DOMContentLoaded",
            enableBackendValidationTestingMode,
            { once: true }
        );
    } else {
        enableBackendValidationTestingMode();
    }
}
