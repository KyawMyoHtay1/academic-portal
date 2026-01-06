<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="portal-gradient flex min-h-screen flex-col items-center pt-6 sm:justify-center sm:pt-0"
    >
        <div class="w-full max-w-md px-6">
            <div class="flex flex-col items-center text-center text-slate-100">
                <Link href="/" class="mb-4 inline-flex items-center justify-center">
                    <ApplicationLogo class="h-16 w-16 fill-current text-portal-gold" />
                </Link>
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-300">
                    University Academic Portal
                </p>
                <h1 class="mt-2 text-2xl font-semibold">
                    Sign in to continue
                </h1>
                <p class="mt-1 text-sm text-slate-300">
                    Access registration, courses, grades, fees, timetable and more from one place.
                </p>
            </div>

            <div
                class="mt-6 w-full overflow-hidden rounded-2xl bg-white/95 px-6 py-6 shadow-xl ring-1 ring-slate-200"
            >
                <slot />
            </div>

            <p class="mt-4 text-center text-xs text-slate-200">
                BSc (Hons) Computing – University Academic Portal Project
            </p>
        </div>
    </div>
</template>
