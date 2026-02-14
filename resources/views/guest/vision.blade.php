@extends('layouts.guest')

@section('title', 'Our Vision')

@push('styles')
<style>
    @keyframes visionRise {
        from {
            opacity: 0;
            transform: translateY(14px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .vision-rise {
        animation: visionRise 0.6s ease-out both;
    }

    .vision-rise:nth-child(2) { animation-delay: 0.08s; }
    .vision-rise:nth-child(3) { animation-delay: 0.16s; }
</style>
@endpush

@section('content')
<div class="container mx-auto max-w-7xl space-y-14 px-4 py-8">
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-900 via-[color:var(--portal-navy)] to-violet-900 px-6 py-14 text-white shadow-2xl ring-2 ring-white/10 md:px-12 md:py-20">
        <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full bg-[color:var(--portal-gold)]/25 blur-3xl"></div>
        <div class="absolute -bottom-20 -left-16 h-64 w-64 rounded-full bg-blue-500/20 blur-3xl"></div>

        <div class="relative z-10 mx-auto max-w-4xl text-center">
            <p class="inline-flex items-center rounded-full bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.14em] text-amber-200 ring-1 ring-white/20">
                Vision Statement
            </p>
            <h1 class="mt-4 text-4xl font-bold leading-tight md:text-6xl">
                Advancing Education Through
                <span class="mt-2 block text-transparent bg-gradient-to-r from-[color:var(--portal-gold)] to-amber-300 bg-clip-text">
                    Secure Digital Innovation
                </span>
            </h1>
            <p class="mx-auto mt-5 max-w-3xl text-base leading-relaxed text-slate-200 md:text-lg">
                Our vision is to make academic processes simple, transparent, and reliable for students, teachers, and administrators. We want every user to move through important academic tasks with clarity, confidence, and minimal friction.
            </p>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('guest.courses') }}" class="inline-flex items-center rounded-full bg-white px-6 py-3 text-sm font-semibold text-[color:var(--portal-navy)] shadow-md hover:bg-slate-100">
                    Explore Courses
                </a>
                <a href="{{ route('guest.feedback') }}" class="inline-flex items-center rounded-full border border-white/40 bg-white/10 px-6 py-3 text-sm font-semibold text-white hover:bg-white/20">
                    Share Feedback
                </a>
            </div>
        </div>
    </section>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">Portal Users</p>
            <p class="mt-2 text-3xl font-bold text-blue-900">{{ $stats['totalUsers'] > 0 ? number_format($stats['totalUsers']) . '+' : '0' }}</p>
        </div>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Enrollments</p>
            <p class="mt-2 text-3xl font-bold text-emerald-900">{{ $stats['totalEnrollments'] > 0 ? number_format($stats['totalEnrollments']) : '0' }}</p>
        </div>
        <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <p class="text-xs font-semibold uppercase tracking-wide text-indigo-700">Courses</p>
            <p class="mt-2 text-3xl font-bold text-indigo-900">{{ $stats['totalCourses'] > 0 ? number_format($stats['totalCourses']) : '0' }}</p>
        </div>
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Direction</p>
            <p class="mt-2 text-3xl font-bold text-amber-900">Future Ready</p>
        </div>
    </section>

    @include('guest.partials.quick-links-strip')

    @include('guest.partials.icon-highlights')

    <section class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md md:p-10">
        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[color:var(--portal-navy)]">Our Vision</p>
        <h2 class="mt-2 text-3xl font-bold text-[color:var(--portal-navy)] md:text-4xl">A trusted digital backbone for academic life</h2>
        <div class="mt-5 space-y-4 text-sm leading-relaxed text-slate-600 md:text-base">
            <p>
                The University Academic Portal is designed to provide a secure, efficient, and user-focused environment for managing teaching, learning, and academic administration at scale.
            </p>
            <p>
                We aim to reduce manual overhead, improve communication across roles, and keep academic records accurate, auditable, and easy to access. This allows faculty and staff to spend less time on fragmented processes and more time on student outcomes, curriculum quality, and innovation.
            </p>
        </div>
    </section>

    <section class="space-y-6">
        <div class="max-w-3xl">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[color:var(--portal-navy)]">Vision In Practice</p>
            <h2 class="mt-2 text-3xl font-bold text-[color:var(--portal-navy)]">How this direction translates into daily experience</h2>
            <p class="mt-3 text-sm leading-relaxed text-slate-600 md:text-base">
                Our long-term vision is executed through measurable improvements in reliability, data quality, and user confidence. Every feature decision is tied to academic outcomes and operational clarity.
            </p>
        </div>
        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 text-indigo-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2a4 4 0 014-4h8m0 0l-3-3m3 3l-3 3M7 7h10a4 4 0 014 4v6a2 2 0 01-2 2H7a4 4 0 01-4-4V9a2 2 0 012-2z" />
                    </svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-slate-900">Reliable Access</h3>
                <p class="mt-2 text-sm text-slate-600">Stable service delivery with predictable performance during peak periods such as enrollment and results release.</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-blue-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                    </svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-slate-900">Transparent Records</h3>
                <p class="mt-2 text-sm text-slate-600">Role-based visibility of grades, attendance, enrollments, and fees to reduce confusion and repetitive support requests.</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-slate-900">Continuous Improvement</h3>
                <p class="mt-2 text-sm text-slate-600">Iterative enhancements driven by feedback, service analytics, and institutional priorities each academic cycle.</p>
            </div>
        </div>
    </section>

    <section class="space-y-6">
        <div class="max-w-2xl">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[color:var(--portal-navy)]">Focus Areas</p>
            <h2 class="mt-2 text-3xl font-bold text-[color:var(--portal-navy)]">Where our vision leads us next</h2>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div class="vision-rise rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <h3 class="text-lg font-semibold text-slate-900">Digital Innovation</h3>
                <p class="mt-2 text-sm text-slate-600">
                    Expand secure integrations, performance analytics, and automation that simplify daily academic workflows.
                </p>
            </div>
            <div class="vision-rise rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <h3 class="text-lg font-semibold text-slate-900">Personalized Experience</h3>
                <p class="mt-2 text-sm text-slate-600">
                    Deliver role-aware dashboards so students, teachers, and staff can act faster with clearer information.
                </p>
            </div>
            <div class="vision-rise rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <h3 class="text-lg font-semibold text-slate-900">Global Collaboration</h3>
                <p class="mt-2 text-sm text-slate-600">
                    Support cross-campus, hybrid, and international academic partnerships with reliable digital tools.
                </p>
            </div>
        </div>
    </section>

    <section class="rounded-3xl border border-amber-200 bg-gradient-to-r from-amber-100 via-amber-50 to-amber-100 p-8 text-center shadow-md ring-1 ring-amber-200/50 md:p-12">
        <h2 class="text-3xl font-bold text-[color:var(--portal-navy)]">Help us shape the next phase</h2>
        <p class="mx-auto mt-3 max-w-2xl text-sm text-slate-700 md:text-base">
            Explore programs, share feedback, or contact our team to learn how the portal supports your academic journey.
        </p>
        <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
            <a href="{{ route('guest.courses') }}" class="inline-flex items-center rounded-full bg-[color:var(--portal-navy)] px-6 py-3 text-sm font-semibold text-white hover:bg-slate-800">
                Browse Courses
            </a>
            <a href="{{ route('guest.feedback') }}" class="inline-flex items-center rounded-full border border-[color:var(--portal-navy)] bg-white px-6 py-3 text-sm font-semibold text-[color:var(--portal-navy)] hover:bg-slate-50">
                Share Feedback
            </a>
        </div>
    </section>
</div>
@endsection




