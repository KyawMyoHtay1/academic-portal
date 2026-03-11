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

    @media (prefers-reduced-motion: reduce) {
        .vision-rise {
            animation: none !important;
        }
    }
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
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-200/80 text-blue-800">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
            </div>
            <p class="mt-3 text-xs font-semibold uppercase tracking-wide text-blue-700">Portal Users</p>
            <p class="mt-1 text-3xl font-bold text-blue-900">{{ $stats['totalUsers'] > 0 ? number_format($stats['totalUsers']) . '+' : '0' }}</p>
        </div>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-200/80 text-emerald-800">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
            </div>
            <p class="mt-3 text-xs font-semibold uppercase tracking-wide text-emerald-700">Approved Enrollments</p>
            <p class="mt-1 text-3xl font-bold text-emerald-900">{{ $stats['totalEnrollments'] > 0 ? number_format($stats['totalEnrollments']) : '0' }}</p>
        </div>
        <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-200/80 text-indigo-800">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
            </div>
            <p class="mt-3 text-xs font-semibold uppercase tracking-wide text-indigo-700">Courses</p>
            <p class="mt-1 text-3xl font-bold text-indigo-900">{{ $stats['totalCourses'] > 0 ? number_format($stats['totalCourses']) : '0' }}</p>
        </div>
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-200/80 text-amber-800">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
            </div>
            <p class="mt-3 text-xs font-semibold uppercase tracking-wide text-amber-700">Direction</p>
            <p class="mt-1 text-3xl font-bold text-amber-900">Future Ready</p>
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
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 text-indigo-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-slate-900">Digital Innovation</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">
                    We will expand secure integrations, performance analytics, and automation that simplify daily academic workflows so faculty and staff spend less time on manual tasks and more on teaching and student success.
                </p>
            </div>
            <div class="vision-rise rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-blue-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-slate-900">Personalized Experience</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">
                    We will deliver role-aware dashboards and workflows so students, teachers, and staff can act faster with clearer, more relevant information and fewer steps to complete common tasks.
                </p>
            </div>
            <div class="vision-rise rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1.61a2 2 0 01.427 5.546 2 2 0 01-1.954 2.94 2 2 0 01-1.427-1.954V13a2 2 0 012-2h.945M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 0a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-slate-900">Global Collaboration</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">
                    We will support cross-campus, hybrid, and international academic partnerships with reliable digital tools so the institution can scale collaboration and mobility without sacrificing quality or security.
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



