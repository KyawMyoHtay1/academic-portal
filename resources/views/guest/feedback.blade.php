@extends('layouts.guest')

@section('title', 'Feedback & Suggestions')

@push('styles')
<style>
    @keyframes feedbackRise {
        from {
            opacity: 0;
            transform: translateY(14px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .feedback-rise {
        animation: feedbackRise 0.6s ease-out both;
    }

    .feedback-rise:nth-child(2) { animation-delay: 0.08s; }
    .feedback-rise:nth-child(3) { animation-delay: 0.16s; }

    @media (prefers-reduced-motion: reduce) {
        .feedback-rise {
            animation: none !important;
        }
    }
</style>
@endpush

@section('content')
<div class="container mx-auto max-w-7xl space-y-14 px-4 py-8">
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-purple-900 via-[color:var(--portal-navy)] to-fuchsia-900 px-6 py-14 text-white shadow-2xl ring-2 ring-white/10 md:px-12 md:py-20">
        <div class="absolute -right-20 -top-20 h-72 w-72 rounded-full bg-[color:var(--portal-gold)]/25 blur-3xl"></div>
        <div class="absolute -bottom-20 -left-16 h-72 w-72 rounded-full bg-blue-500/20 blur-3xl"></div>

        <div class="relative z-10 mx-auto max-w-4xl text-center">
            <p class="inline-flex items-center rounded-full bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.14em] text-amber-200 ring-1 ring-white/20">
                Feedback Center
            </p>
            <h1 class="mt-4 text-4xl font-bold leading-tight md:text-6xl">
                Share Feedback
                <span class="mt-2 block text-transparent bg-gradient-to-r from-[color:var(--portal-gold)] to-amber-300 bg-clip-text">
                    Improve The Portal
                </span>
            </h1>
            <p class="mx-auto max-w-3xl text-base leading-relaxed text-slate-200 md:text-lg">
                Your suggestions and issue reports help us improve usability, performance, and academic service quality. Feedback from students, teachers, and administrative teams directly informs product priorities, workflow improvements, and support planning so the portal stays aligned with real usage and institutional goals.
            </p>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('guest.contact') }}" class="inline-flex items-center rounded-full bg-white px-6 py-3 text-sm font-semibold text-[color:var(--portal-navy)] shadow-md hover:bg-slate-100">
                    Contact Us
                </a>
                <a href="{{ route('guest.support') }}" class="inline-flex items-center rounded-full border border-white/40 bg-white/10 px-6 py-3 text-sm font-semibold text-white hover:bg-white/20">
                    Get Support
                </a>
            </div>
        </div>
    </section>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-200/80 text-blue-800">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5m-4 0h5" /></svg>
            </div>
            <p class="mt-3 text-xs font-semibold uppercase tracking-wide text-blue-700">Submission Type</p>
            <p class="mt-1 text-3xl font-bold text-blue-900">Open</p>
        </div>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-200/80 text-emerald-800">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <p class="mt-3 text-xs font-semibold uppercase tracking-wide text-emerald-700">Review Flow</p>
            <p class="mt-1 text-3xl font-bold text-emerald-900">Staff Inbox</p>
        </div>
        <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-200/80 text-indigo-800">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            </div>
            <p class="mt-3 text-xs font-semibold uppercase tracking-wide text-indigo-700">Audience</p>
            <p class="mt-1 text-3xl font-bold text-indigo-900">All Users</p>
        </div>
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-200/80 text-amber-800">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
            </div>
            <p class="mt-3 text-xs font-semibold uppercase tracking-wide text-amber-700">Purpose</p>
            <p class="mt-1 text-3xl font-bold text-amber-900">Improvements</p>
        </div>
    </section>

    @include('guest.partials.quick-links-strip')

    @include('guest.partials.icon-highlights')

    <section class="grid gap-6 lg:grid-cols-[1.2fr,0.8fr]">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 md:p-8">
            <div class="mb-6">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[color:var(--portal-navy)]">Submit Feedback</p>
                <h2 class="mt-2 text-2xl font-bold text-[color:var(--portal-navy)] md:text-3xl">Tell us what should improve</h2>
                <p class="mt-2 text-sm text-slate-600">Use the form below to share suggestions, report issues, or leave compliments. Clear, specific feedback helps us investigate quickly and apply improvements where they matter most.</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-red-800">
                    <p class="text-sm font-semibold">Please fix the errors below</p>
                    <ul class="mt-2 list-disc pl-5 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('guest.feedback.store') }}" class="space-y-5" data-recaptcha-action="feedback">
                @csrf
                @if (config('recaptcha.site_key'))
                    <input type="hidden" name="recaptcha_token" value="">
                @endif

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label for="feedbackName" class="mb-2 block text-sm font-semibold text-slate-700">Your Name <span class="text-red-500">*</span></label>
                        <input
                            type="text"
                            id="feedbackName"
                            name="name"
                            required
                            maxlength="150"
                            value="{{ old('name') }}"
                            class="block w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 placeholder-slate-400 focus:border-[color:var(--portal-navy)] focus:outline-none focus:ring-2 focus:ring-[color:var(--portal-navy)]"
                            placeholder="John Doe"
                        >
                    </div>
                    <div>
                        <label for="feedbackEmail" class="mb-2 block text-sm font-semibold text-slate-700">Email Address <span class="text-red-500">*</span></label>
                        <input
                            type="email"
                            id="feedbackEmail"
                            name="email"
                            required
                            maxlength="255"
                            value="{{ old('email') }}"
                            class="block w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 placeholder-slate-400 focus:border-[color:var(--portal-navy)] focus:outline-none focus:ring-2 focus:ring-[color:var(--portal-navy)]"
                            placeholder="john.doe@example.com"
                        >
                    </div>
                </div>

                <div>
                    <label for="feedbackType" class="mb-2 block text-sm font-semibold text-slate-700">Feedback Type <span class="text-red-500">*</span></label>
                    <select
                        id="feedbackType"
                        name="type"
                        required
                        class="block w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:border-[color:var(--portal-navy)] focus:outline-none focus:ring-2 focus:ring-[color:var(--portal-navy)]"
                    >
                        <option value="">Select feedback type</option>
                        <option value="suggestion" @selected(old('type') === 'suggestion')>Suggestion</option>
                        <option value="issue" @selected(old('type') === 'issue')>Report Issue</option>
                        <option value="compliment" @selected(old('type') === 'compliment')>Compliment</option>
                        <option value="other" @selected(old('type') === 'other')>Other</option>
                    </select>
                </div>

                <div>
                    <label for="feedbackMessage" class="mb-2 block text-sm font-semibold text-slate-700">Your Feedback <span class="text-red-500">*</span></label>
                    <textarea
                        id="feedbackMessage"
                        name="message"
                        rows="6"
                        required
                        maxlength="5000"
                        class="block w-full resize-none rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 placeholder-slate-400 focus:border-[color:var(--portal-navy)] focus:outline-none focus:ring-2 focus:ring-[color:var(--portal-navy)]"
                        placeholder="Share your suggestion, report an issue, or describe your experience..."
                    >{{ old('message') }}</textarea>
                </div>

                <button
                    type="submit"
                    class="inline-flex w-full items-center justify-center rounded-lg bg-[color:var(--portal-navy)] px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-slate-800"
                >
                    Submit Feedback
                </button>
            </form>
        </div>

        <div class="space-y-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 text-amber-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                </div>
                <p class="mt-3 text-xs font-semibold uppercase tracking-[0.14em] text-[color:var(--portal-navy)]">Why It Matters</p>
                <h3 class="mt-2 text-xl font-bold text-slate-900">Your input drives product decisions</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">We review every submission to identify high-impact improvements for students, staff, and teachers. Clear feedback helps us prioritize fixes, refine workflows, and plan new features that match how you actually use the portal.</p>
            </div>

            <div class="feedback-rise rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-blue-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                </div>
                <h4 class="mt-4 text-lg font-semibold text-slate-900">Improve Functionality</h4>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">Suggest new features, workflow enhancements, or better ways to complete common tasks. We use these ideas when planning the next release and improving the academic experience for everyone.</p>
            </div>
            <div class="feedback-rise rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-100 text-red-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
                <h4 class="mt-4 text-lg font-semibold text-slate-900">Report Issues</h4>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">Flag bugs, errors, or confusing interactions as soon as you notice them. Include the page or feature name and what you expected so we can investigate and fix issues quickly.</p>
            </div>
            <div class="feedback-rise rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <h4 class="mt-4 text-lg font-semibold text-slate-900">Shape Experience</h4>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">Tell us what works well or what could be clearer. Your perspective helps us build a more intuitive, efficient academic platform for the whole university community.</p>
            </div>
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md md:p-10">
        <div class="max-w-3xl">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[color:var(--portal-navy)]">What Makes Feedback Useful</p>
            <h2 class="mt-2 text-3xl font-bold text-[color:var(--portal-navy)]">Include context, impact, and expected outcome</h2>
            <p class="mt-3 text-sm leading-relaxed text-slate-600 md:text-base">
                High-quality feedback helps us prioritize the right fixes and enhancements. Sharing where the issue occurred, what happened, and what result you expected allows teams to reproduce and resolve problems faster.
            </p>
        </div>
        <div class="mt-6 grid gap-4 md:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-100 text-blue-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h6m-8 8h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="mt-3 text-sm font-semibold text-slate-900">Describe the page or feature</h3>
                <p class="mt-1 text-xs text-slate-600">Mention the exact area (for example: grades page, timetable screen, or payment status section).</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h8m-8 4h5m4 7H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="mt-3 text-sm font-semibold text-slate-900">Explain what happened</h3>
                <p class="mt-1 text-xs text-slate-600">Share actual behavior, any error message shown, and whether the issue is repeatable.</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-100 text-amber-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <h3 class="mt-3 text-sm font-semibold text-slate-900">State the expected result</h3>
                <p class="mt-1 text-xs text-slate-600">Tell us what you expected to see so we can align fixes with user needs and academic workflows.</p>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
@if (config('recaptcha.site_key'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var siteKey = @json(config('recaptcha.site_key'));
        var form = document.querySelector('form[data-recaptcha-action="feedback"]');
        if (!siteKey || !form) return;

        var tokenInput = form.querySelector('input[name="recaptcha_token"]');
        if (!tokenInput) return;

        var submitButton = form.querySelector('button[type="submit"], input[type="submit"]');
        var isSubmitting = false;

        function setButtonState(disabled) {
            if (!submitButton) return;
            submitButton.disabled = !!disabled;
        }

        function showClientError(message) {
            var existing = form.querySelector('[data-recaptcha-client-error]');
            if (existing) existing.remove();

            var node = document.createElement('div');
            node.setAttribute('data-recaptcha-client-error', '1');
            node.className = 'rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800';
            node.textContent = message;
            form.prepend(node);
        }

        function clearClientError() {
            var existing = form.querySelector('[data-recaptcha-client-error]');
            if (existing) existing.remove();
        }

        function fail(message) {
            tokenInput.value = '';
            setButtonState(false);
            showClientError(message || 'reCAPTCHA verification failed. Please try again.');
        }

        form.addEventListener('submit', function (event) {
            if (isSubmitting) return;

            event.preventDefault();
            clearClientError();
            setButtonState(true);

            if (!window.grecaptcha || typeof window.grecaptcha.ready !== 'function') {
                fail('reCAPTCHA failed to load. Please refresh the page and try again.');
                return;
            }

            window.grecaptcha.ready(function () {
                window.grecaptcha.execute(siteKey, { action: 'feedback' })
                    .then(function (token) {
                        if (!token) {
                            fail('reCAPTCHA verification failed. Please try again.');
                            return;
                        }

                        tokenInput.value = token;
                        isSubmitting = true;
                        form.submit();
                    })
                    .catch(function () {
                        fail('reCAPTCHA verification failed. Please try again.');
                    });
            });
        });
    });
</script>
@endif
@endpush

