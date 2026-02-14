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
</style>
@endpush

@section('content')
<div class="container mx-auto space-y-14 px-4 py-8">
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[color:var(--portal-navy)] via-slate-800 to-[color:var(--portal-navy)] px-6 py-14 text-white shadow-xl md:px-12 md:py-20">
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
            <p class="mx-auto mt-5 max-w-3xl text-base leading-relaxed text-slate-200 md:text-lg">
                Your suggestions and issue reports help us improve usability, performance, and academic service quality.
            </p>
        </div>
    </section>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">Submission Type</p>
            <p class="mt-2 text-3xl font-bold text-blue-900">Open</p>
        </div>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Response Window</p>
            <p class="mt-2 text-3xl font-bold text-emerald-900">Fast</p>
        </div>
        <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-indigo-700">Audience</p>
            <p class="mt-2 text-3xl font-bold text-indigo-900">All Users</p>
        </div>
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Impact</p>
            <p class="mt-2 text-3xl font-bold text-amber-900">Continuous</p>
        </div>
    </section>

    @include('guest.partials.quick-links-strip')

    <section class="grid gap-6 lg:grid-cols-[1.2fr,0.8fr]">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm md:p-8">
            <div class="mb-6">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[color:var(--portal-navy)]">Submit Feedback</p>
                <h2 class="mt-2 text-2xl font-bold text-[color:var(--portal-navy)] md:text-3xl">Tell us what should improve</h2>
                <p class="mt-2 text-sm text-slate-600">Use the form below to share suggestions, report issues, or leave compliments.</p>
            </div>

            @if (session('success'))
                <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">
                    <p class="text-sm font-semibold">Feedback submitted</p>
                    <p class="mt-1 text-sm">{{ session('success') }}</p>
                </div>
            @endif

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
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[color:var(--portal-navy)]">Why It Matters</p>
                <h3 class="mt-2 text-xl font-bold text-slate-900">Your input drives product decisions</h3>
                <p class="mt-2 text-sm text-slate-600">We review submissions to identify high-impact improvements for students, staff, and teachers.</p>
            </div>

            <div class="feedback-rise rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h4 class="text-lg font-semibold text-slate-900">Improve Functionality</h4>
                <p class="mt-2 text-sm text-slate-600">Suggest features and workflow enhancements.</p>
            </div>
            <div class="feedback-rise rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h4 class="text-lg font-semibold text-slate-900">Report Issues</h4>
                <p class="mt-2 text-sm text-slate-600">Flag bugs or confusing interactions quickly.</p>
            </div>
            <div class="feedback-rise rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h4 class="text-lg font-semibold text-slate-900">Shape Experience</h4>
                <p class="mt-2 text-sm text-slate-600">Help us build a more intuitive academic platform.</p>
            </div>
        </div>
    </section>
</div>
@endsection
