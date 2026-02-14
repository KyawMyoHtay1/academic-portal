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
</style>
@endpush

@section('content')
<div class="container mx-auto space-y-14 px-4 py-8">
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[color:var(--portal-navy)] via-slate-800 to-[color:var(--portal-navy)] px-6 py-14 text-white shadow-xl md:px-12 md:py-20">
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
            <p class="mx-auto mt-5 max-w-3xl text-base leading-relaxed text-slate-200 md:text-lg">
                Contact admissions, academics, support, or student services. Send us a message and we will respond as quickly as possible.
            </p>
        </div>
    </section>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">Users Reached</p>
            <p class="mt-2 text-3xl font-bold text-blue-900">{{ number_format(data_get($stats, 'totalStudents', 0) + data_get($stats, 'totalFaculty', 0)) }}</p>
        </div>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Faculty</p>
            <p class="mt-2 text-3xl font-bold text-emerald-900">{{ number_format(data_get($stats, 'totalFaculty', 0)) }}</p>
        </div>
        <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-indigo-700">Courses</p>
            <p class="mt-2 text-3xl font-bold text-indigo-900">{{ number_format(data_get($stats, 'totalCourses', 0)) }}</p>
        </div>
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Office Status</p>
            <p class="mt-2 text-3xl font-bold text-amber-900">Open Hours</p>
        </div>
    </section>

    @include('guest.partials.quick-links-strip')
    @include('guest.partials.image-cards-contact')

    <section class="grid gap-6 lg:grid-cols-[1.1fr,0.9fr]">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm md:p-8">
            <div class="mb-6">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[color:var(--portal-navy)]">Send Message</p>
                <h2 class="mt-2 text-2xl font-bold text-[color:var(--portal-navy)] md:text-3xl">Contact our team</h2>
                <p class="mt-2 text-sm text-slate-600">Complete the form and include enough detail so we can respond effectively.</p>
            </div>

            @if (session('success'))
                <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">
                    <p class="text-sm font-semibold">Message sent</p>
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
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[color:var(--portal-navy)]">Contact Details</p>
                <h3 class="mt-2 text-xl font-bold text-slate-900">Reach us directly</h3>
                <div class="mt-4 space-y-3 text-sm text-slate-700">
                    <p><span class="font-semibold">Email:</span> info@university.edu</p>
                    <p><span class="font-semibold">Phone:</span> +123 456 7890</p>
                    <p><span class="font-semibold">Address:</span> 123 University Avenue, City, Country</p>
                    <p><span class="font-semibold">Hours:</span> Mon-Fri, 9:00 AM - 5:00 PM</p>
                </div>
            </div>

            <div class="contact-rise rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h4 class="text-lg font-semibold text-slate-900">Admissions</h4>
                <p class="mt-2 text-sm text-slate-600">admissions@university.edu</p>
            </div>
            <div class="contact-rise rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h4 class="text-lg font-semibold text-slate-900">Academic Affairs</h4>
                <p class="mt-2 text-sm text-slate-600">academics@university.edu</p>
            </div>
            <div class="contact-rise rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h4 class="text-lg font-semibold text-slate-900">IT Support</h4>
                <p class="mt-2 text-sm text-slate-600">itsupport@university.edu</p>
            </div>
        </div>
    </section>

    <section class="rounded-3xl border border-amber-200 bg-gradient-to-r from-amber-100 via-amber-50 to-amber-100 p-8 text-center shadow-sm md:p-12">
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
