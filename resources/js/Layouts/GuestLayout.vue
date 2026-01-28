<script setup>
import PortalLogo from "@/Components/PortalLogo.vue";
import GoogleTranslate from "@/Components/GoogleTranslate.vue";
import { Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const page = usePage();
const isFullWidthPage = computed(
    () =>
        typeof page.url === "string" && page.url.startsWith("/privacy-policy"),
);
const isRegisterPage = computed(
    () => typeof page.url === "string" && page.url.includes("/register"),
);
</script>

<template>
    <div class="portal-gradient flex min-h-screen flex-col">
        <!-- Language Selector (top right) -->
        <div class="fixed top-4 right-4 z-50">
            <GoogleTranslate />
        </div>

        <!-- Full-width layout for Privacy Policy etc. -->
        <template v-if="isFullWidthPage">
            <header
                class="shrink-0 border-b border-white/10 bg-[color:var(--portal-navy)]/80 backdrop-blur-sm"
            >
                <div
                    class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8"
                >
                    <Link
                        href="/"
                        class="flex items-center gap-3 text-slate-100 hover:text-portal-gold transition-colors"
                    >
                        <PortalLogo class="h-10 w-10 shrink-0" />
                        <span
                            class="text-sm font-semibold uppercase tracking-wide"
                            >University Portal</span
                        >
                    </Link>
                    <Link
                        :href="route('privacy-policy')"
                        class="text-xs text-slate-300 hover:text-portal-gold transition-colors"
                    >
                        Privacy Policy
                    </Link>
                </div>
            </header>
            <main class="min-h-0 flex-1 w-full">
                <slot />
            </main>
            <footer
                class="shrink-0 border-t border-white/10 px-4 py-3 text-center text-xs text-slate-400"
            >
                BSc (Hons) Computing – University Academic Portal Project
            </footer>
        </template>

        <!-- Narrow centered layout for login etc. -->
        <template v-else>
            <div
                class="flex flex-1 flex-col items-center pt-6 sm:justify-center sm:pt-10"
            >
                <div class="w-full max-w-lg px-6 sm:px-8">
                    <div
                        class="flex flex-col items-center text-center text-slate-100"
                    >
                        <Link
                            href="/"
                            class="portal-logo-link mb-4 inline-flex items-center justify-center transition-transform hover:scale-105"
                        >
                            <PortalLogo class="h-16 w-16 shrink-0" />
                        </Link>
                        <p
                            class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-300"
                        >
                            University Portal
                        </p>
                        <h1 class="mt-2 text-2xl font-semibold">
                            {{
                                isRegisterPage
                                    ? "Create your account"
                                    : "Sign in to continue"
                            }}
                        </h1>
                        <p class="mt-1 text-sm text-slate-300">
                            {{
                                isRegisterPage
                                    ? "Register for courses, grades, fees, timetable and more."
                                    : "Access registration, courses, grades, fees, timetable and more from one place."
                            }}
                        </p>
                    </div>

                    <div
                        class="mt-6 w-full overflow-hidden rounded-2xl bg-white/95 px-8 py-8 shadow-xl ring-1 ring-slate-200"
                    >
                        <slot />
                    </div>

                    <p class="mt-4 text-center text-xs text-slate-200">
                        BSc (Hons) Computing – University Academic Portal
                        Project
                    </p>

                    <div
                        class="mt-4 flex items-center justify-center gap-3 text-xs"
                    >
                        <Link
                            :href="route('privacy-policy')"
                            class="text-slate-300 hover:text-portal-gold transition-colors"
                        >
                            Privacy Policy
                        </Link>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>
