@extends('layouts.guest')

@section('title', 'Support & Help Desk')

@push('styles')
<style>
    @keyframes supportRise {
        from {
            opacity: 0;
            transform: translateY(14px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .support-rise {
        animation: supportRise 0.6s ease-out both;
    }

    .support-rise:nth-child(2) { animation-delay: 0.08s; }
    .support-rise:nth-child(3) { animation-delay: 0.16s; }
    .support-rise:nth-child(4) { animation-delay: 0.24s; }

    @media (prefers-reduced-motion: reduce) {
        .support-rise {
            animation: none !important;
        }
    }
</style>
@endpush

@section('content')
<div class="container mx-auto max-w-7xl space-y-14 px-4 py-8">
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-orange-900 via-[color:var(--portal-navy)] to-amber-900 px-6 py-14 text-white shadow-2xl ring-2 ring-white/10 md:px-12 md:py-20">
        <div class="absolute -right-20 -top-20 h-72 w-72 rounded-full bg-[color:var(--portal-gold)]/25 blur-3xl"></div>
        <div class="absolute -bottom-20 -left-16 h-72 w-72 rounded-full bg-blue-500/20 blur-3xl"></div>

        <div class="relative z-10 mx-auto max-w-4xl text-center">
            <p class="inline-flex items-center rounded-full bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.14em] text-amber-200 ring-1 ring-white/20">
                Help Desk
            </p>
            <h1 class="mt-4 text-4xl font-bold leading-tight md:text-6xl">
                Support And
                <span class="mt-2 block text-transparent bg-gradient-to-r from-[color:var(--portal-gold)] to-amber-300 bg-clip-text">
                    Troubleshooting
                </span>
            </h1>
            <p class="mx-auto max-w-3xl text-base leading-relaxed text-slate-200 md:text-lg">
                Get quick answers, practical guides, and direct support for any question about the University Academic Portal. Whether you are resolving an urgent access issue or requesting process guidance, our support model is designed for clarity and fast turnaround so you can get back to learning and teaching with minimal disruption.
            </p>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('guest.contact') }}" class="inline-flex items-center rounded-full bg-white px-6 py-3 text-sm font-semibold text-[color:var(--portal-navy)] shadow-md hover:bg-slate-100">
                    Contact Support
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
            <p class="mt-3 text-xs font-semibold uppercase tracking-wide text-blue-700">Registered Users</p>
            <p class="mt-1 text-3xl font-bold text-blue-900">{{ number_format(data_get($stats, 'totalUsers', 0)) }}</p>
        </div>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-200/80 text-emerald-800">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
            </div>
            <p class="mt-3 text-xs font-semibold uppercase tracking-wide text-emerald-700">Students</p>
            <p class="mt-1 text-3xl font-bold text-emerald-900">{{ number_format(data_get($stats, 'totalStudents', 0)) }}</p>
        </div>
        <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-200/80 text-indigo-800">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
            </div>
            <p class="mt-3 text-xs font-semibold uppercase tracking-wide text-indigo-700">Support Team</p>
            <p class="mt-1 text-3xl font-bold text-indigo-900">{{ number_format(data_get($stats, 'supportTeamCount', 0)) }}</p>
        </div>
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-200/80 text-amber-800">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <p class="mt-3 text-xs font-semibold uppercase tracking-wide text-amber-700">Access</p>
            <p class="mt-1 text-3xl font-bold text-amber-900">Public</p>
        </div>
    </section>

    @include('guest.partials.quick-links-strip')

    @include('guest.partials.icon-highlights')

    <section class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md md:p-10">
        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[color:var(--portal-navy)]">Support Overview</p>
        <h2 class="mt-2 text-3xl font-bold text-[color:var(--portal-navy)] md:text-4xl">Fast help for portal access and academic workflows</h2>
        <div class="mt-5 space-y-4 text-sm leading-relaxed text-slate-600 md:text-base">
            <p>
                The Help Desk supports login issues, role access, timetable questions, grade visibility, fee pages, and service navigation across all portal roles.
            </p>
            <p>
                Start with self-help resources when possible, then contact support for direct investigation and follow-up. Our objective is to keep service interruptions short and communicate resolution progress clearly.
            </p>
        </div>
    </section>

    @include('guest.partials.image-cards-support')

    <section class="space-y-6">
        <div class="max-w-2xl">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[color:var(--portal-navy)]">Support Channels</p>
            <h2 class="mt-2 text-3xl font-bold text-[color:var(--portal-navy)]">Choose the best way to get help</h2>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="support-rise rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-blue-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-slate-900">Help Guides</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">Start with step-by-step help for common portal tasks like login, grades, timetables, fees, and day-to-day navigation before opening a support request.</p>
                <a href="{{ route('guest.user-manual.page', [], false) }}" class="mt-3 inline-flex text-sm font-semibold text-[color:var(--portal-navy)] hover:text-blue-600">Open Help Guides</a>
            </div>
            <div class="support-rise rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-slate-900">User Guides</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">Step-by-step walkthroughs for key tasks: enrollment, grading, attendance, fee checks, and more. Follow along to complete processes correctly the first time.</p>
                <div class="mt-3 flex items-center gap-4">
                    <a href="{{ route('guest.user-manual.page', [], false) }}" class="inline-flex text-sm font-semibold text-[color:var(--portal-navy)] hover:text-emerald-600">Open User Manual</a>
                    <a href="{{ route('guest.user-manual.download', [], false) }}" download data-skip-loading class="inline-flex text-sm font-semibold text-emerald-700 hover:text-emerald-800">Download PDF</a>
                </div>
            </div>
            <div class="support-rise rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-violet-100 text-violet-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-slate-900">Report Issues</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">Submit technical problems, incorrect data, or broken workflows. Include the page, what you did, and what you expected so we can investigate and fix quickly.</p>
                <a href="{{ route('guest.contact') }}" class="mt-3 inline-flex text-sm font-semibold text-[color:var(--portal-navy)] hover:text-purple-600">Report Issue</a>
            </div>
            <div class="support-rise rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 text-amber-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-slate-900">Direct Contact</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">Reach our support desk for urgent access issues, account-specific problems, or when self-help and guides are not enough. We route your request to the right team.</p>
                <a href="{{ route('guest.contact') }}" class="mt-3 inline-flex text-sm font-semibold text-[color:var(--portal-navy)] hover:text-amber-600">Contact Support</a>
            </div>
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md md:p-10">
        <div class="max-w-3xl">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[color:var(--portal-navy)]">Issue Priorities</p>
            <h2 class="mt-2 text-3xl font-bold text-[color:var(--portal-navy)]">How requests are triaged and resolved</h2>
            <p class="mt-3 text-sm leading-relaxed text-slate-600 md:text-base">
                Support tickets are processed according to academic impact, urgency, and scope. This helps us resolve critical issues quickly while maintaining transparent communication for all request types.
            </p>
        </div>
        <div class="mt-6 grid gap-4 md:grid-cols-3">
            <div class="flex gap-3 rounded-2xl border border-red-200 bg-red-50 p-5">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-red-200 text-red-800">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-red-700">High Priority</p>
                    <h3 class="mt-1 text-sm font-semibold text-red-900">Login and access failures</h3>
                    <p class="mt-2 text-xs leading-relaxed text-red-800">Account lockouts, role permission errors, and service unavailability affecting coursework. These are triaged and addressed as soon as possible.</p>
                </div>
            </div>
            <div class="flex gap-3 rounded-2xl border border-amber-200 bg-amber-50 p-5">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-amber-200 text-amber-800">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Medium Priority</p>
                    <h3 class="mt-1 text-sm font-semibold text-amber-900">Data mismatch and workflow issues</h3>
                    <p class="mt-2 text-xs leading-relaxed text-amber-800">Incorrect timetable entries, missing course links, and delayed record updates. Resolved in order of impact and queue position.</p>
                </div>
            </div>
            <div class="flex gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-200 text-emerald-800">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Standard Priority</p>
                    <h3 class="mt-1 text-sm font-semibold text-emerald-900">General guidance requests</h3>
                    <p class="mt-2 text-xs leading-relaxed text-emerald-800">Navigation help, feature questions, and best-practice support for common tasks. Handled in turn with clear communication.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md md:p-10">
        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[color:var(--portal-navy)]">Recommended Path</p>
        <h2 class="mt-2 text-3xl font-bold text-[color:var(--portal-navy)]">How to resolve issues quickly</h2>
        <div class="mt-6 grid gap-4 md:grid-cols-4">
            <div class="flex gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-blue-700 font-bold">1</div>
                <div>
                    <p class="text-sm font-semibold text-slate-900">Check Guides</p>
                    <p class="mt-1 text-xs leading-relaxed text-slate-600">Review self-help instructions first; many login, grade, and timetable tasks are already covered there.</p>
                </div>
            </div>
            <div class="flex gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700 font-bold">2</div>
                <div>
                    <p class="text-sm font-semibold text-slate-900">Open Guide</p>
                    <p class="mt-1 text-xs leading-relaxed text-slate-600">Follow task-specific instructions for enrollment, grading, or other workflows so you can complete the task correctly.</p>
                </div>
            </div>
            <div class="flex gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-amber-700 font-bold">3</div>
                <div>
                    <p class="text-sm font-semibold text-slate-900">Report Issue</p>
                    <p class="mt-1 text-xs leading-relaxed text-slate-600">Share details, steps to reproduce, and screenshots so the support team can investigate and resolve quickly.</p>
                </div>
            </div>
            <div class="flex gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-indigo-100 text-indigo-700 font-bold">4</div>
                <div>
                    <p class="text-sm font-semibold text-slate-900">Track Resolution</p>
                    <p class="mt-1 text-xs leading-relaxed text-slate-600">Get status updates from support until the issue is resolved. We aim to keep you informed at each stage.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-3xl border border-amber-200 bg-gradient-to-r from-amber-100 via-amber-50 to-amber-100 p-8 text-center shadow-md ring-1 ring-amber-200/50 md:p-12">
        <h2 class="text-3xl font-bold text-[color:var(--portal-navy)]">Still need help?</h2>
        <p class="mx-auto mt-3 max-w-2xl text-sm text-slate-700 md:text-base">
            Contact our support team and include your role, issue details, and any screenshots to speed up resolution.
        </p>
        <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
            <a href="{{ route('guest.contact') }}" class="inline-flex items-center rounded-full bg-[color:var(--portal-navy)] px-6 py-3 text-sm font-semibold text-white hover:bg-slate-800">
                Contact Support
            </a>
            <a href="{{ route('guest.feedback') }}" class="inline-flex items-center rounded-full border border-[color:var(--portal-navy)] bg-white px-6 py-3 text-sm font-semibold text-[color:var(--portal-navy)] hover:bg-slate-50">
                Share Feedback
            </a>
        </div>
    </section>
</div>
@endsection
