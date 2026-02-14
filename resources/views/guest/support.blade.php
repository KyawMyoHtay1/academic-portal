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
</style>
@endpush

@section('content')
<div class="container mx-auto space-y-14 px-4 py-8">
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[color:var(--portal-navy)] via-slate-800 to-[color:var(--portal-navy)] px-6 py-14 text-white shadow-xl md:px-12 md:py-20">
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
            <p class="mx-auto mt-5 max-w-3xl text-base leading-relaxed text-slate-200 md:text-lg">
                Get quick answers, practical guides, and direct support for any question about the University Academic Portal.
            </p>
        </div>
    </section>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">Active Users</p>
            <p class="mt-2 text-3xl font-bold text-blue-900">{{ number_format(data_get($stats, 'totalUsers', 0)) }}</p>
        </div>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Students Supported</p>
            <p class="mt-2 text-3xl font-bold text-emerald-900">{{ number_format(data_get($stats, 'totalStudents', 0)) }}</p>
        </div>
        <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-indigo-700">Support Team</p>
            <p class="mt-2 text-3xl font-bold text-indigo-900">{{ number_format(data_get($stats, 'totalFaculty', 0)) }}</p>
        </div>
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Service Status</p>
            <p class="mt-2 text-3xl font-bold text-amber-900">Available</p>
        </div>
    </section>

    @include('guest.partials.quick-links-strip')

    <section class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm md:p-10">
        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[color:var(--portal-navy)]">Support Overview</p>
        <h2 class="mt-2 text-3xl font-bold text-[color:var(--portal-navy)] md:text-4xl">Fast help for portal access and academic workflows</h2>
        <div class="mt-5 space-y-4 text-sm leading-relaxed text-slate-600 md:text-base">
            <p>
                The Help Desk supports login issues, role access, timetable questions, grade visibility, fee pages, and service navigation.
            </p>
            <p>
                Start with self-help resources when possible, then contact support for direct investigation and follow-up.
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
            <div class="support-rise rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-slate-900">FAQs</h3>
                <p class="mt-2 text-sm text-slate-600">Quick answers for common questions and day-to-day usage.</p>
                <a href="{{ route('guest.contact') }}" class="mt-3 inline-flex text-sm font-semibold text-[color:var(--portal-navy)] hover:text-blue-600">Browse FAQs</a>
            </div>
            <div class="support-rise rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-slate-900">User Guides</h3>
                <p class="mt-2 text-sm text-slate-600">Step-by-step walkthroughs for key academic portal tasks.</p>
                <a href="{{ route('guest.contact') }}" class="mt-3 inline-flex text-sm font-semibold text-[color:var(--portal-navy)] hover:text-emerald-600">View Guides</a>
            </div>
            <div class="support-rise rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-slate-900">Report Issues</h3>
                <p class="mt-2 text-sm text-slate-600">Submit technical problems or incorrect data reports.</p>
                <a href="{{ route('guest.contact') }}" class="mt-3 inline-flex text-sm font-semibold text-[color:var(--portal-navy)] hover:text-purple-600">Report Issue</a>
            </div>
            <div class="support-rise rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-slate-900">Direct Contact</h3>
                <p class="mt-2 text-sm text-slate-600">Reach our support desk for urgent or account-specific help.</p>
                <a href="{{ route('guest.contact') }}" class="mt-3 inline-flex text-sm font-semibold text-[color:var(--portal-navy)] hover:text-amber-600">Contact Support</a>
            </div>
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm md:p-10">
        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[color:var(--portal-navy)]">Recommended Path</p>
        <h2 class="mt-2 text-3xl font-bold text-[color:var(--portal-navy)]">How to resolve issues quickly</h2>
        <div class="mt-6 grid gap-4 md:grid-cols-4">
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-700">Step 1</p>
                <p class="mt-1 text-sm font-semibold text-slate-900">Check FAQs</p>
                <p class="mt-1 text-xs text-slate-600">Review common solutions first.</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-700">Step 2</p>
                <p class="mt-1 text-sm font-semibold text-slate-900">Open Guide</p>
                <p class="mt-1 text-xs text-slate-600">Follow task-specific instructions.</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-700">Step 3</p>
                <p class="mt-1 text-sm font-semibold text-slate-900">Report Issue</p>
                <p class="mt-1 text-xs text-slate-600">Share details and screenshots.</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-700">Step 4</p>
                <p class="mt-1 text-sm font-semibold text-slate-900">Track Resolution</p>
                <p class="mt-1 text-xs text-slate-600">Get status updates from support.</p>
            </div>
        </div>
    </section>

    <section class="rounded-3xl border border-amber-200 bg-gradient-to-r from-amber-100 via-amber-50 to-amber-100 p-8 text-center shadow-sm md:p-12">
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
