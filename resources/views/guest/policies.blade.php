@extends('layouts.guest')

@section('title', 'Academic Policies & Guidelines')

@push('styles')
<style>
    @keyframes policyRise {
        from {
            opacity: 0;
            transform: translateY(14px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .policy-rise {
        animation: policyRise 0.6s ease-out both;
    }

    .policy-rise:nth-child(2) { animation-delay: 0.08s; }
    .policy-rise:nth-child(3) { animation-delay: 0.16s; }
    .policy-rise:nth-child(4) { animation-delay: 0.24s; }
</style>
@endpush

@section('content')
<div class="container mx-auto max-w-7xl space-y-14 px-4 py-8">
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-[color:var(--portal-navy)] to-blue-900 px-6 py-14 text-white shadow-2xl ring-2 ring-white/10 md:px-12 md:py-20">
        <div class="absolute -right-20 -top-20 h-72 w-72 rounded-full bg-[color:var(--portal-gold)]/25 blur-3xl"></div>
        <div class="absolute -bottom-20 -left-16 h-72 w-72 rounded-full bg-blue-500/20 blur-3xl"></div>

        <div class="relative z-10 mx-auto max-w-4xl text-center">
            <p class="inline-flex items-center rounded-full bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.14em] text-amber-200 ring-1 ring-white/20">
                Policy Hub
            </p>
            <h1 class="mt-4 text-4xl font-bold leading-tight md:text-6xl">
                Academic Policies
                <span class="mt-2 block text-transparent bg-gradient-to-r from-[color:var(--portal-gold)] to-amber-300 bg-clip-text">
                    And Guidelines
                </span>
            </h1>
            <p class="mx-auto max-w-3xl text-base leading-relaxed text-slate-200 md:text-lg">
                Access the standards that govern assessment, attendance, grading, conduct, and academic integrity across the university. These guidelines help ensure fair treatment, consistent decisions, and a shared understanding of responsibilities for students, faculty, and staff in every department and program.
            </p>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('privacy-policy') }}" class="inline-flex items-center rounded-full bg-white px-6 py-3 text-sm font-semibold text-[color:var(--portal-navy)] shadow-md hover:bg-slate-100">
                    Privacy Policy
                </a>
                <a href="{{ route('guest.support') }}" class="inline-flex items-center rounded-full border border-white/40 bg-white/10 px-6 py-3 text-sm font-semibold text-white hover:bg-white/20">
                    Support & Help
                </a>
            </div>
        </div>
    </section>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-200/80 text-blue-800">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
            </div>
            <p class="mt-3 text-xs font-semibold uppercase tracking-wide text-blue-700">Policy Scope</p>
            <p class="mt-1 text-3xl font-bold text-blue-900">Campus Wide</p>
        </div>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-200/80 text-emerald-800">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
            </div>
            <p class="mt-3 text-xs font-semibold uppercase tracking-wide text-emerald-700">Compliance</p>
            <p class="mt-1 text-3xl font-bold text-emerald-900">Required</p>
        </div>
        <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-200/80 text-indigo-800">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
            </div>
            <p class="mt-3 text-xs font-semibold uppercase tracking-wide text-indigo-700">Update Cadence</p>
            <p class="mt-1 text-3xl font-bold text-indigo-900">Termly</p>
        </div>
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-200/80 text-amber-800">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            </div>
            <p class="mt-3 text-xs font-semibold uppercase tracking-wide text-amber-700">Effective Date</p>
            <p class="mt-1 text-3xl font-bold text-amber-900">Current</p>
        </div>
    </section>

    @include('guest.partials.quick-links-strip')

    @include('guest.partials.icon-highlights')

    <section class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md md:p-10">
        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[color:var(--portal-navy)]">Overview</p>
        <h2 class="mt-2 text-3xl font-bold text-[color:var(--portal-navy)] md:text-4xl">A clear framework for fair academic practice</h2>
        <div class="mt-5 space-y-4 text-sm leading-relaxed text-slate-600 md:text-base">
            <p>
                This page provides the official policy framework used by students, teachers, and staff to manage academic responsibilities consistently and transparently across faculties and departments.
            </p>
            <p>
                Policies are reviewed periodically to reflect institutional requirements, legal obligations, and quality assurance standards. Where needed, updates are coordinated with academic leadership so policy language remains practical, enforceable, and easy to interpret.
            </p>
        </div>
    </section>

    <section class="space-y-6">
        <div class="max-w-3xl">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[color:var(--portal-navy)]">Policy Lifecycle</p>
            <h2 class="mt-2 text-3xl font-bold text-[color:var(--portal-navy)]">How university policies are created and maintained</h2>
            <p class="mt-3 text-sm leading-relaxed text-slate-600 md:text-base">
                Policies are not static documents. They are reviewed with academic leaders, compliance teams, and operations staff to ensure regulations remain current, practical, and fair for all users.
            </p>
        </div>
        <div class="grid gap-4 md:grid-cols-4">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-700">01</p>
                <h3 class="mt-1 text-sm font-semibold text-slate-900">Drafting</h3>
                <p class="mt-2 text-xs text-slate-600">Academic and operational teams define scope, obligations, and expected behaviors.</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-700">02</p>
                <h3 class="mt-1 text-sm font-semibold text-slate-900">Review</h3>
                <p class="mt-2 text-xs text-slate-600">Cross-functional review confirms legal alignment, enforceability, and clarity.</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-700">03</p>
                <h3 class="mt-1 text-sm font-semibold text-slate-900">Publication</h3>
                <p class="mt-2 text-xs text-slate-600">Approved policy updates are communicated through portal announcements and guidance pages.</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-700">04</p>
                <h3 class="mt-1 text-sm font-semibold text-slate-900">Monitoring</h3>
                <p class="mt-2 text-xs text-slate-600">Feedback and operational data inform refinements for the next review cycle.</p>
            </div>
        </div>
    </section>

    <section class="space-y-6">
        <div class="max-w-2xl">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[color:var(--portal-navy)]">Policy Areas</p>
            <h2 class="mt-2 text-3xl font-bold text-[color:var(--portal-navy)]">Key guidelines at a glance</h2>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="policy-rise rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-blue-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-slate-900">Assessment Rules</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">Submission standards, deadlines, moderation processes, and re-assessment procedures so every student is evaluated fairly and consistently across courses and terms.</p>
            </div>
            <div class="policy-rise rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" /></svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-slate-900">Grading Systems</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">Grade bands, calculation methods, and result publication protocols that align with institutional and national quality assurance requirements.</p>
            </div>
            <div class="policy-rise rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 text-amber-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-slate-900">Attendance</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">Minimum attendance thresholds, approved absence handling, and documentation requirements to support both student success and academic accountability.</p>
            </div>
            <div class="policy-rise rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 text-indigo-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-slate-900">Academic Integrity</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">Expectations for originality, referencing, and ethical conduct in coursework, exams, and research so the institution maintains trust and high standards.</p>
            </div>
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md md:p-10">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="max-w-3xl">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[color:var(--portal-navy)]">Privacy And Data</p>
                <h2 class="mt-2 text-2xl font-bold text-[color:var(--portal-navy)] md:text-3xl">Privacy policy summary</h2>
                <p class="mt-4 text-sm leading-relaxed text-slate-600 md:text-base">
                    We protect personal and academic data through secure access controls, role-based permissions, and lawful processing standards. Data is used only for institutional operations, communication, and compliance obligations.
                </p>
            </div>
            <a
                href="{{ route('privacy-policy') }}"
                class="inline-flex items-center rounded-md bg-[color:var(--portal-navy)] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-slate-800"
            >
                View Full Privacy Policy
            </a>
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-2">
            <div class="flex gap-4 rounded-xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-blue-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-slate-900">Your Rights</h3>
                    <p class="mt-1 text-sm leading-relaxed text-slate-600">You have the right to access, correct, request lawful deletion of your data, and where applicable to data portability. Requests are processed in line with institutional and regulatory timelines.</p>
                </div>
            </div>
            <div class="flex gap-4 rounded-xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-slate-900">Security Controls</h3>
                    <p class="mt-1 text-sm leading-relaxed text-slate-600">We use authentication, encryption for sensitive workflows, and regular security monitoring to protect personal and academic data across the portal.</p>
                </div>
            </div>
            <div class="flex gap-4 rounded-xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-amber-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-slate-900">Data Retention</h3>
                    <p class="mt-1 text-sm leading-relaxed text-slate-600">Academic and administrative records are retained according to institutional policy and legal requirements. Retention schedules are reviewed periodically.</p>
                </div>
            </div>
            <div class="flex gap-4 rounded-xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-indigo-100 text-indigo-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-slate-900">Contact</h3>
                    <p class="mt-1 text-sm leading-relaxed text-slate-600">For privacy or data-protection queries, contact our support team or email privacy@university.edu. We aim to respond within the timeframe set out in our policy.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-3xl border border-amber-200 bg-gradient-to-r from-amber-100 via-amber-50 to-amber-100 p-8 text-center shadow-md ring-1 ring-amber-200/50 md:p-12">
        <h2 class="text-3xl font-bold text-[color:var(--portal-navy)]">Need help understanding a policy?</h2>
        <p class="mx-auto mt-3 max-w-2xl text-sm text-slate-700 md:text-base">
            Contact the academic support team for clarification on regulations, procedures, and student responsibilities.
        </p>
        <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
            <a href="{{ route('guest.support') }}" class="inline-flex items-center rounded-full bg-[color:var(--portal-navy)] px-6 py-3 text-sm font-semibold text-white hover:bg-slate-800">
                Support & Help Desk
            </a>
            <a href="{{ route('guest.contact') }}" class="inline-flex items-center rounded-full border border-[color:var(--portal-navy)] bg-white px-6 py-3 text-sm font-semibold text-[color:var(--portal-navy)] hover:bg-slate-50">
                Contact Us
            </a>
        </div>
    </section>
</div>
@endsection




