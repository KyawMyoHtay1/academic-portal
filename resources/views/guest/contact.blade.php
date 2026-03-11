@extends('layouts.guest')

@section('title', 'Contact Us')

@push('styles')
<style>
    @keyframes contactRise {
        from {
            opacity: 0;
            transform: translateY(14px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .contact-rise {
        animation: contactRise 0.6s ease-out both;
    }

    .contact-rise:nth-child(2) { animation-delay: 0.08s; }
    .contact-rise:nth-child(3) { animation-delay: 0.16s; }
    .contact-rise:nth-child(4) { animation-delay: 0.24s; }

    @media (prefers-reduced-motion: reduce) {
        .contact-rise {
            animation: none !important;
        }
    }
</style>
@endpush

@section('content')
<div class="container mx-auto max-w-7xl space-y-14 px-4 py-8">
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-teal-900 via-[color:var(--portal-navy)] to-cyan-900 px-6 py-14 text-white shadow-2xl ring-2 ring-white/10 md:px-12 md:py-20">
        <div class="absolute -right-20 -top-20 h-72 w-72 rounded-full bg-[color:var(--portal-gold)]/25 blur-3xl"></div>
        <div class="absolute -bottom-20 -left-16 h-72 w-72 rounded-full bg-blue-500/20 blur-3xl"></div>

        <div class="relative z-10 mx-auto max-w-4xl text-center">
            <p class="inline-flex items-center rounded-full bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.14em] text-amber-200 ring-1 ring-white/20">
                Contact Center
            </p>
            <h1 class="mt-4 text-4xl font-bold leading-tight md:text-6xl">
                Get In Touch
                <span class="mt-2 block text-transparent bg-gradient-to-r from-[color:var(--portal-gold)] to-amber-300 bg-clip-text">
                    We Are Ready To Help
                </span>
            </h1>
            <p class="mx-auto max-w-3xl text-base leading-relaxed text-slate-200 md:text-lg">
                Contact admissions, academics, support, or student services through one clear channel. Send us a message with context and we will route it to the right team for timely follow-up so you get accurate answers without bouncing between departments.
            </p>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('guest.support') }}" class="inline-flex items-center rounded-full bg-white px-6 py-3 text-sm font-semibold text-[color:var(--portal-navy)] shadow-md hover:bg-slate-100">
                    Help Desk
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
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
            </div>
            <p class="mt-3 text-xs font-semibold uppercase tracking-wide text-indigo-700">Faculty</p>
            <p class="mt-1 text-3xl font-bold text-indigo-900">{{ number_format(data_get($stats, 'totalFaculty', 0)) }}</p>
        </div>
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-200/80 text-amber-800">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <p class="mt-3 text-xs font-semibold uppercase tracking-wide text-amber-700">Courses</p>
            <p class="mt-1 text-3xl font-bold text-amber-900">{{ number_format(data_get($stats, 'totalCourses', 0)) }}</p>
        </div>
    </section>

    @include('guest.partials.quick-links-strip')

    @include('guest.partials.icon-highlights')
    @include('guest.partials.image-cards-contact')

    <section class="grid gap-6 lg:grid-cols-[1.1fr,0.9fr]">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 md:p-8">
            <div class="mb-6">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[color:var(--portal-navy)]">Send Message</p>
                <h2 class="mt-2 text-2xl font-bold text-[color:var(--portal-navy)] md:text-3xl">Contact our team</h2>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">Complete the form and include enough detail, such as your role, the topic (admissions, grades, support, and similar), and what you need so we can respond effectively. Specific requests help us reduce delays and provide more accurate guidance on the first response.</p>
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

            <form method="POST" action="{{ route('guest.contact.store') }}" class="space-y-5" data-recaptcha-action="contact">
                @csrf
                @if (config('recaptcha.site_key'))
                    <input type="hidden" name="recaptcha_token" value="">
                @endif

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label for="firstName" class="mb-2 block text-sm font-semibold text-slate-700">First Name <span class="text-red-500">*</span></label>
                        <input
                            type="text"
                            id="firstName"
                            name="firstName"
                            required
                            maxlength="100"
                            value="{{ old('firstName') }}"
                            class="block w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 placeholder-slate-400 focus:border-[color:var(--portal-navy)] focus:outline-none focus:ring-2 focus:ring-[color:var(--portal-navy)]"
                            placeholder="John"
                        >
                    </div>
                    <div>
                        <label for="lastName" class="mb-2 block text-sm font-semibold text-slate-700">Last Name <span class="text-red-500">*</span></label>
                        <input
                            type="text"
                            id="lastName"
                            name="lastName"
                            required
                            maxlength="100"
                            value="{{ old('lastName') }}"
                            class="block w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 placeholder-slate-400 focus:border-[color:var(--portal-navy)] focus:outline-none focus:ring-2 focus:ring-[color:var(--portal-navy)]"
                            placeholder="Doe"
                        >
                    </div>
                </div>

                <div>
                    <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">Email Address <span class="text-red-500">*</span></label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        required
                        maxlength="255"
                        value="{{ old('email') }}"
                        class="block w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 placeholder-slate-400 focus:border-[color:var(--portal-navy)] focus:outline-none focus:ring-2 focus:ring-[color:var(--portal-navy)]"
                        placeholder="john.doe@example.com"
                    >
                </div>

                <div>
                    <label for="phone" class="mb-2 block text-sm font-semibold text-slate-700">Phone Number</label>
                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        maxlength="30"
                        value="{{ old('phone') }}"
                        class="block w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 placeholder-slate-400 focus:border-[color:var(--portal-navy)] focus:outline-none focus:ring-2 focus:ring-[color:var(--portal-navy)]"
                        placeholder="+123 456 7890"
                    >
                </div>

                <div>
                    <label for="subject" class="mb-2 block text-sm font-semibold text-slate-700">Subject <span class="text-red-500">*</span></label>
                    <input
                        type="text"
                        id="subject"
                        name="subject"
                        required
                        maxlength="100"
                        value="{{ old('subject') }}"
                        class="block w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 placeholder-slate-400 focus:border-[color:var(--portal-navy)] focus:outline-none focus:ring-2 focus:ring-[color:var(--portal-navy)]"
                        placeholder="How can we help?"
                    >
                </div>

                <div>
                    <label for="message" class="mb-2 block text-sm font-semibold text-slate-700">Message <span class="text-red-500">*</span></label>
                    <textarea
                        id="message"
                        name="message"
                        rows="6"
                        required
                        maxlength="5000"
                        class="block w-full resize-none rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 placeholder-slate-400 focus:border-[color:var(--portal-navy)] focus:outline-none focus:ring-2 focus:ring-[color:var(--portal-navy)]"
                        placeholder="Tell us how we can help you..."
                    >{{ old('message') }}</textarea>
                </div>

                <button
                    type="submit"
                    class="inline-flex w-full items-center justify-center rounded-lg bg-[color:var(--portal-navy)] px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-slate-800"
                >
                    Send Message
                </button>
            </form>
        </div>

        <div class="space-y-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-slate-100 text-slate-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                </div>
                <p class="mt-3 text-xs font-semibold uppercase tracking-[0.14em] text-[color:var(--portal-navy)]">Contact Guidance</p>
                <h3 class="mt-2 text-xl font-bold text-slate-900">How to reach us</h3>
                <p class="mt-2 text-sm text-slate-600">Use the form on this page for routed responses, or open Support & Help Desk for technical troubleshooting and access problems.</p>
                <div class="mt-4 space-y-3 text-sm text-slate-700">
                    <p><span class="font-semibold">General inquiries:</span> Submit the form on this page.</p>
                    <p><span class="font-semibold">Technical support:</span> Use Support & Help Desk for login and portal issues.</p>
                    <p><span class="font-semibold">Message routing:</span> Contact requests are forwarded to the staff team.</p>
                    <p><span class="font-semibold">Office details:</span> Refer to official university channels for current visit and office-hour information.</p>
                </div>
            </div>

            <div class="contact-rise rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-blue-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                </div>
                <h4 class="mt-4 text-lg font-semibold text-slate-900">Admissions</h4>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">Use this form for applications, requirements, and enrollment questions. Put "Admissions" in the subject line so the request can be routed quickly.</p>
            </div>
            <div class="contact-rise rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" /></svg>
                </div>
                <h4 class="mt-4 text-lg font-semibold text-slate-900">Academic Affairs</h4>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">Use this form for curriculum, program, and academic policy questions. For course or grade issues, include your student ID and course code.</p>
            </div>
            <div class="contact-rise rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 text-amber-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                </div>
                <h4 class="mt-4 text-lg font-semibold text-slate-900">IT Support</h4>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">Use Support & Help Desk for login issues, portal errors, and access problems. For urgent access blocks, include "Urgent" in your subject line.</p>
            </div>
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md md:p-10">
        <div class="max-w-3xl">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[color:var(--portal-navy)]">Before You Submit</p>
            <h2 class="mt-2 text-3xl font-bold text-[color:var(--portal-navy)]">Send clearer requests for faster responses</h2>
            <p class="mt-3 text-sm leading-relaxed text-slate-600 md:text-base">
                Including the right information helps our team route your message to the correct department and reduce back-and-forth delays. For technical issues, include your role and the page where the issue occurred.
            </p>
        </div>
        <div class="mt-6 grid gap-4 md:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                <h3 class="text-sm font-semibold text-slate-900">Select a clear subject</h3>
                <p class="mt-2 text-xs text-slate-600">Use a concise subject line such as "Fee receipt issue" or "Enrollment approval delay".</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                <h3 class="text-sm font-semibold text-slate-900">Provide key details</h3>
                <p class="mt-2 text-xs text-slate-600">Mention your role, affected course or module, and any deadline impacted.</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                <h3 class="text-sm font-semibold text-slate-900">Add supporting context</h3>
                <p class="mt-2 text-xs text-slate-600">Include reference numbers or screenshots when applicable so we can investigate quickly.</p>
            </div>
        </div>
    </section>

    <section class="rounded-3xl border border-amber-200 bg-gradient-to-r from-amber-100 via-amber-50 to-amber-100 p-8 text-center shadow-md ring-1 ring-amber-200/50 md:p-12">
        <h2 class="text-3xl font-bold text-[color:var(--portal-navy)]">Need help with the portal right now?</h2>
        <p class="mx-auto mt-3 max-w-2xl text-sm text-slate-700 md:text-base">
            Use Support & Help Desk resources for faster troubleshooting and direct technical assistance.
        </p>
        <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
            <a href="{{ route('guest.support') }}" class="inline-flex items-center rounded-full bg-[color:var(--portal-navy)] px-6 py-3 text-sm font-semibold text-white hover:bg-slate-800">
                Open Help Desk
            </a>
            <a href="{{ route('guest.feedback') }}" class="inline-flex items-center rounded-full border border-[color:var(--portal-navy)] bg-white px-6 py-3 text-sm font-semibold text-[color:var(--portal-navy)] hover:bg-slate-50">
                Share Feedback
            </a>
        </div>
    </section>
</div>
@endsection

@push('scripts')
@if (config('recaptcha.site_key'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var siteKey = @json(config('recaptcha.site_key'));
        var form = document.querySelector('form[data-recaptcha-action="contact"]');
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
                window.grecaptcha.execute(siteKey, { action: 'contact' })
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
