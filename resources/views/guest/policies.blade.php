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
            <p class="mx-auto mt-5 max-w-3xl text-base leading-relaxed text-slate-200 md:text-lg">
                Access the standards that govern assessment, attendance, grading, conduct, and academic integrity across the university.
            </p>
        </div>
    </section>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">Policy Scope</p>
            <p class="mt-2 text-3xl font-bold text-blue-900">Campus Wide</p>
        </div>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Compliance</p>
            <p class="mt-2 text-3xl font-bold text-emerald-900">Required</p>
        </div>
        <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <p class="text-xs font-semibold uppercase tracking-wide text-indigo-700">Update Cadence</p>
            <p class="mt-2 text-3xl font-bold text-indigo-900">Termly</p>
        </div>
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Effective Date</p>
            <p class="mt-2 text-3xl font-bold text-amber-900">Current</p>
        </div>
    </section>

    @include('guest.partials.quick-links-strip')

    @include('guest.partials.icon-highlights')

    <section class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md md:p-10">
        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[color:var(--portal-navy)]">Overview</p>
        <h2 class="mt-2 text-3xl font-bold text-[color:var(--portal-navy)] md:text-4xl">A clear framework for fair academic practice</h2>
        <div class="mt-5 space-y-4 text-sm leading-relaxed text-slate-600 md:text-base">
            <p>
                This page provides the official policy framework used by students, teachers, and staff to manage academic responsibilities consistently and transparently.
            </p>
            <p>
                Policies are reviewed periodically to reflect institutional requirements, legal obligations, and quality assurance standards.
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
                <h3 class="text-lg font-semibold text-slate-900">Assessment Rules</h3>
                <p class="mt-2 text-sm text-slate-600">Submission standards, timelines, moderation, and re-assessment procedures.</p>
            </div>
            <div class="policy-rise rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <h3 class="text-lg font-semibold text-slate-900">Grading Systems</h3>
                <p class="mt-2 text-sm text-slate-600">Grade bands, calculation methods, and result publication protocols.</p>
            </div>
            <div class="policy-rise rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <h3 class="text-lg font-semibold text-slate-900">Attendance</h3>
                <p class="mt-2 text-sm text-slate-600">Minimum attendance thresholds and approved absence handling.</p>
            </div>
            <div class="policy-rise rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <h3 class="text-lg font-semibold text-slate-900">Academic Integrity</h3>
                <p class="mt-2 text-sm text-slate-600">Expectations for originality, referencing, and ethical conduct.</p>
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
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <h3 class="text-sm font-semibold text-slate-900">Your Rights</h3>
                <p class="mt-1 text-sm text-slate-600">Access, correction, lawful deletion requests, and data portability where applicable.</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <h3 class="text-sm font-semibold text-slate-900">Security Controls</h3>
                <p class="mt-1 text-sm text-slate-600">Authentication, encryption of sensitive workflows, and regular security monitoring.</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <h3 class="text-sm font-semibold text-slate-900">Data Retention</h3>
                <p class="mt-1 text-sm text-slate-600">Academic records are retained according to institutional and legal requirements.</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <h3 class="text-sm font-semibold text-slate-900">Contact</h3>
                <p class="mt-1 text-sm text-slate-600">For policy queries, contact support or email privacy@university.edu.</p>
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




