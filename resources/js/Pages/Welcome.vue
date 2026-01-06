<script setup>
import { Head, Link } from "@inertiajs/vue3";

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

function handleImageError() {
    document.getElementById("screenshot-container")?.classList.add("!hidden");
    document.getElementById("docs-card")?.classList.add("!row-span-1");
    document.getElementById("docs-card-content")?.classList.add("!flex-row");
    document.getElementById("background")?.classList.add("!hidden");
}
</script>

<template>
    <Head title="Welcome" />
    <div class="portal-gradient min-h-screen text-slate-100">
        <div
            class="relative mx-auto flex min-h-screen max-w-6xl flex-col justify-between px-6 py-8 lg:px-10"
        >
            <header class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-xl bg-portal-gold/10 ring-1 ring-portal-gold/40"
                    >
                        <span class="text-lg font-bold text-portal-gold">
                            UA
                        </span>
                    </div>
                    <div>
                        <p
                            class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-300"
                        >
                            University Academic Portal
                        </p>
                        <p class="text-sm font-medium text-slate-100">
                            BSc (Hons) Computing – Final Year Project
                        </p>
                    </div>
                </div>

                <nav v-if="canLogin" class="flex items-center gap-2 text-sm">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="rounded-full bg-white/10 px-3 py-1.5 font-medium text-slate-100 ring-1 ring-white/30 transition hover:bg-white/20"
                    >
                        Go to dashboard
                    </Link>

                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="rounded-full bg-white px-3 py-1.5 font-medium text-slate-900 shadow-sm ring-1 ring-slate-200 transition hover:bg-slate-100"
                        >
                            Log in
                        </Link>

                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="rounded-full bg-transparent px-3 py-1.5 font-medium text-slate-100 ring-1 ring-white/40 transition hover:bg-white/10"
                        >
                            Register
                        </Link>
                    </template>
                </nav>
            </header>

            <main
                class="mt-10 grid gap-10 lg:grid-cols-[3fr,2fr] lg:items-center"
            >
                <section>
                    <p class="portal-badge bg-portal-gold/10 text-portal-gold">
                        University digital transformation
                    </p>
                    <h1
                        class="mt-4 text-3xl font-semibold leading-tight sm:text-4xl lg:text-5xl"
                    >
                        A unified portal for
                        <span class="text-portal-gold">academic management</span
                        >.
                    </h1>
                    <p class="mt-4 max-w-xl text-sm text-slate-200">
                        This system is designed to replace manual paper-based
                        processes with a central web portal. It brings together
                        Student Registration, Course Registration, Grade
                        Submission, Fee Payment, Timetable Management,
                        Attendance Tracking and Communication.
                    </p>

                    <dl class="mt-6 grid gap-4 text-sm sm:grid-cols-2">
                        <div
                            class="rounded-xl bg-black/10 p-4 ring-1 ring-white/10"
                        >
                            <dt
                                class="text-xs font-semibold uppercase tracking-wide text-slate-300"
                            >
                                Built with
                            </dt>
                            <dd class="mt-1 text-slate-100">
                                Laravel, Inertia.js, Vue.js & Tailwind CSS
                            </dd>
                        </div>
                        <div
                            class="rounded-xl bg-black/10 p-4 ring-1 ring-white/10"
                        >
                            <dt
                                class="text-xs font-semibold uppercase tracking-wide text-slate-300"
                            >
                                Project focus
                            </dt>
                            <dd class="mt-1 text-slate-100">
                                University Academic Portal – process automation
                                & decision support
                            </dd>
                        </div>
                    </dl>
                </section>

                <section class="space-y-4">
                    <div class="portal-card border-0 bg-white/95 p-5">
                        <p
                            class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                        >
                            Core modules
                        </p>
                        <ul class="mt-3 space-y-2 text-sm text-slate-700">
                            <li>
                                • Student Registration & Course Registration
                            </li>
                            <li>• Grade Submission & Online Result Viewing</li>
                            <li>• Fee Payment & Financial tracking</li>
                            <li>
                                • Timetable Management & Attendance Tracking
                            </li>
                            <li>• Announcements, messaging & notifications</li>
                        </ul>
                    </div>

                    <div class="portal-card border-0 bg-white/95 p-5">
                        <p
                            class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                        >
                            Methodology
                        </p>
                        <p class="mt-2 text-sm text-slate-700">
                            Developed using Agile DSDM with timeboxed iterations
                            for: Analysis, Design, Student & Course
                            Registration, Grades & Fees, Timetable, Attendance,
                            Communication, Deployment and Evaluation.
                        </p>
                    </div>

                    <div
                        class="portal-card border-0 bg-white/95 p-5 text-xs text-slate-600"
                    >
                        <p>
                            Laravel v{{ laravelVersion }} · PHP v{{
                                phpVersion
                            }}
                        </p>
                        <p class="mt-1">
                            Proposal: University Academic Portal using Vue.js
                            and Laravel – Kyaw Myo Htay (001510323), BSc (Hons)
                            Computing (Final Year Entry).
                        </p>
                    </div>
                </section>
            </main>
        </div>
    </div>
</template>
