<script setup>
import { Head, Link } from "@inertiajs/vue3";

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
    publicCourses: {
        type: Array,
        default: () => [],
    },
    publicAnnouncements: {
        type: Array,
        default: () => [],
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

            <main class="mt-10 space-y-10">
                <!-- Hero -->
                <section class="grid gap-10 lg:grid-cols-[3fr,2fr] lg:items-center">
                    <section>
                        <p class="portal-badge bg-portal-gold/10 text-portal-gold">
                            Guest access
                        </p>
                        <h1 class="mt-4 text-3xl font-semibold leading-tight sm:text-4xl lg:text-5xl">
                            Explore our university before you log in.
                        </h1>
                        <p class="mt-4 max-w-2xl text-sm text-slate-200">
                            Browse public courses, announcements, and campus info. Log in or register to access personalised dashboards for students, teachers, and staff.
                        </p>
                        <div class="mt-6 flex flex-wrap gap-3">
                            <Link
                                v-if="canLogin"
                                :href="route('login')"
                                class="rounded-full bg-white px-4 py-2 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-slate-200 transition hover:bg-slate-100"
                            >
                                Log in
                            </Link>
                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="rounded-full bg-transparent px-4 py-2 text-sm font-semibold text-slate-100 ring-1 ring-white/40 transition hover:bg-white/10"
                            >
                                Register
                            </Link>
                        </div>
                    </section>

                    <section class="space-y-4">
                        <div class="portal-card border-0 bg-white/95 p-5">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                What guests can see
                            </p>
                            <ul class="mt-3 space-y-2 text-sm text-slate-700">
                                <li>• Public course overview (read-only)</li>
                                <li>• Latest announcements</li>
                                <li>• Campus location & contact information</li>
                                <li>• Clear login/register for full access</li>
                            </ul>
                        </div>

                        <div class="portal-card border-0 bg-white/95 p-5 text-xs text-slate-600">
                            <p>Laravel v{{ laravelVersion }} · PHP v{{ phpVersion }}</p>
                            <p class="mt-1">
                                Proposal: University Academic Portal using Vue.js and Laravel – BSc (Hons) Computing (Final Year Project).
                            </p>
                        </div>
                    </section>
                </section>

                <!-- About + Location -->
                <section class="grid gap-8 lg:grid-cols-2">
                    <div class="space-y-3">
                        <h2 class="text-2xl font-semibold text-slate-100">About the University</h2>
                        <p class="text-sm text-slate-200 leading-relaxed">
                            We are committed to academic excellence, innovation, and student success. Our mission is to deliver high-quality education and foster a vibrant learning community.
                        </p>
                        <div class="rounded-lg border border-white/10 bg-white/5 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-200">
                                Mission & Vision
                            </p>
                            <p class="mt-1 text-sm text-slate-100">
                                Empower learners to achieve their potential through research-driven teaching and inclusive academic support.
                            </p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <h2 class="text-2xl font-semibold text-slate-100">Location</h2>
                        <p class="text-sm text-slate-200">
                            123 University Avenue, City, Country. Easily reachable via public transport, with nearby amenities and student services.
                        </p>
                        <div class="overflow-hidden rounded-xl border border-white/10 shadow-sm">
                            <iframe
                                class="h-64 w-full"
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3151.8354345093746!2d144.9537363153167!3d-37.81627974202137!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ad65d43f1f1f1f1%3A0xf1f1f1f1f1f1f1f1!2sUniversity!5e0!3m2!1sen!2s!4v1610000000000!5m2!1sen!2s"
                                allowfullscreen=""
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                            ></iframe>
                        </div>
                    </div>
                </section>

                <!-- Public Courses -->
                <section class="space-y-4">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="portal-badge bg-portal-gold/10 text-portal-gold">Courses</p>
                            <h2 class="mt-2 text-2xl font-semibold text-slate-100">
                                Explore our programmes
                            </h2>
                            <p class="text-sm text-slate-200">
                                Browse a snapshot of available courses. Log in to enrol if you are a student.
                            </p>
                        </div>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <div
                            v-for="course in props.publicCourses"
                            :key="course.id"
                            class="rounded-xl border border-white/10 bg-white/5 p-4 shadow-sm"
                        >
                            <p class="text-xs font-semibold uppercase tracking-wide text-portal-gold">
                                {{ course.course_code }}
                            </p>
                            <p class="mt-1 text-lg font-semibold text-white">
                                {{ course.title }}
                            </p>
                            <p class="mt-2 text-sm text-slate-200">
                                Credits: {{ course.credits }} | {{ course.semester }}
                            </p>
                            <p class="mt-3 text-xs text-slate-300">
                                Read-only preview for guests
                            </p>
                        </div>
                        <div v-if="!props.publicCourses.length" class="rounded-xl border border-dashed border-white/20 p-6 text-center text-sm text-slate-200">
                            No courses to display yet.
                        </div>
                    </div>
                </section>

                <!-- Public Announcements -->
                <section class="space-y-4">
                    <p class="portal-badge bg-portal-gold/10 text-portal-gold">Announcements</p>
                    <h2 class="text-2xl font-semibold text-slate-100">Latest updates</h2>
                    <p class="text-sm text-slate-200">
                        Stay informed about campus news and public events.
                    </p>
                    <div class="space-y-3">
                        <div
                            v-for="item in props.publicAnnouncements"
                            :key="item.id"
                            class="rounded-xl border border-white/10 bg-white/5 p-4 shadow-sm"
                        >
                            <div class="flex items-center justify-between gap-3">
                                <p class="text-lg font-semibold text-white">
                                    {{ item.title }}
                                </p>
                                <p class="text-xs text-slate-300">
                                    {{ item.created_at }}
                                </p>
                            </div>
                            <p class="mt-2 text-sm text-slate-200">
                                {{ item.body }}
                            </p>
                        </div>
                        <div v-if="!props.publicAnnouncements.length" class="rounded-xl border border-dashed border-white/20 p-6 text-center text-sm text-slate-200">
                            No announcements to display yet.
                        </div>
                    </div>
                </section>

                <!-- Contact -->
                <section class="rounded-2xl border border-white/10 bg-white/5 p-6 text-slate-100 shadow-sm">
                    <div class="grid gap-6 md:grid-cols-3">
                        <div class="md:col-span-2 space-y-2">
                            <h3 class="text-lg font-semibold">Contact us</h3>
                            <p class="text-sm text-slate-200">
                                Email: info@university.edu | Phone: +123 456 7890
                            </p>
                            <p class="text-sm text-slate-200">
                                Address: 123 University Avenue, City, Country
                            </p>
                        </div>
                        <div class="flex items-center justify-end gap-3">
                            <Link
                                v-if="canLogin"
                                :href="route('login')"
                                class="rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-900 bg-white hover:bg-slate-100"
                            >
                                Log in
                            </Link>
                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="rounded-full bg-portal-gold px-4 py-2 text-sm font-semibold text-portal-navy shadow-sm hover:bg-amber-300"
                            >
                                Register
                            </Link>
                        </div>
                    </div>
                </section>
            </main>

            <!-- Footer -->
            <footer class="mt-12 border-t border-white/10 pt-6 text-center text-xs text-slate-300">
                <div class="flex flex-wrap items-center justify-center gap-4">
                    <Link
                        :href="route('privacy-policy')"
                        class="hover:text-portal-gold transition-colors"
                    >
                        Privacy Policy
                    </Link>
                    <span class="text-slate-500">•</span>
                    <Link
                        :href="route('guest.about')"
                        class="hover:text-portal-gold transition-colors"
                    >
                        About
                    </Link>
                    <span class="text-slate-500">•</span>
                    <Link
                        :href="route('guest.contact')"
                        class="hover:text-portal-gold transition-colors"
                    >
                        Contact
                    </Link>
                </div>
                <p class="mt-3 text-slate-400">
                    © {{ new Date().getFullYear() }} University Academic Portal. All rights reserved.
                </p>
            </footer>
        </div>
    </div>
</template>
