@extends('layouts.guest')

@section('title', 'Contact Us')

@push('styles')
<style>
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.8s ease-out;
    }
    .contact-card {
        animation: fadeInUp 0.8s ease-out;
    }
    .contact-card:nth-child(1) { animation-delay: 0.1s; }
    .contact-card:nth-child(2) { animation-delay: 0.2s; }
    .contact-card:nth-child(3) { animation-delay: 0.3s; }
    .contact-card:nth-child(4) { animation-delay: 0.4s; }
    .form-input:focus {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.1);
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6 space-y-16">
    {{-- Hero Section --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[color:var(--portal-navy)] via-slate-800 to-[color:var(--portal-navy)] px-6 md:px-12 py-16 md:py-24 text-white shadow-2xl">
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-0 right-0 w-96 h-96 bg-[color:var(--portal-gold)] rounded-full mix-blend-multiply filter blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl"></div>
        </div>
        
        <div class="relative z-10 max-w-4xl mx-auto text-center space-y-6">
            <p class="inline-flex items-center gap-2 rounded-full bg-white/10 backdrop-blur-sm px-4 py-2 text-xs font-semibold uppercase tracking-wide text-amber-200 ring-1 ring-white/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Get in Touch
            </p>
            <h1 class="text-5xl md:text-6xl lg:text-7xl font-bold leading-tight">
                We're Here to
                <span class="block text-transparent bg-clip-text bg-gradient-to-r from-[color:var(--portal-gold)] to-amber-300 mt-2">
                    Help You
                </span>
            </h1>
            <p class="text-xl md:text-2xl text-slate-200 max-w-3xl mx-auto leading-relaxed">
                Have questions? Need assistance? Our team is ready to help. Reach out through any of the channels below, and we'll get back to you as soon as possible.
            </p>
        </div>
    </section>

    {{-- Contact & Campus Slider --}}
    <section class="space-y-4">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-[color:var(--portal-navy)] mb-1">Visit & Connect</p>
                <h2 class="text-2xl md:text-3xl font-bold text-[color:var(--portal-navy)]">Campus & Contact</h2>
                <p class="text-sm md:text-base text-slate-600">Add your own campus, office, or support team photos here.</p>
            </div>
        </div>

        <div class="portal-slider rounded-3xl border border-slate-200 bg-slate-900/90 shadow-xl overflow-hidden" data-portal-slider data-autoplay="true" data-interval="7000">
            <div class="portal-slider-track relative h-48 md:h-64 lg:h-72">
                <div class="portal-slide is-active" data-portal-slide>
                    <div class="relative h-full w-full">
                        <div class="h-full w-full bg-cover bg-center" style="background-image: url('{{ asset('images/home/lab.png') }}');"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/40 to-transparent"></div>
                        <div class="absolute inset-0 flex items-end">
                            <div class="p-6 md:p-8 space-y-1 text-white">
                                <h3 class="text-lg md:text-xl font-bold">Main Campus</h3>
                                <p class="text-sm md:text-base text-slate-100/90 max-w-xl">Show the main campus location or front desk.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="portal-slide" data-portal-slide>
                    <div class="relative h-full w-full">
                        <div class="h-full w-full bg-cover bg-center" style="background-image: url('{{ asset('images/home/lab.png') }}');"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/40 to-transparent"></div>
                        <div class="absolute inset-0 flex items-end">
                            <div class="p-6 md:p-8 space-y-1 text-white">
                                <h3 class="text-lg md:text-xl font-bold">Support & Help Desk</h3>
                                <p class="text-sm md:text-base text-slate-100/90 max-w-xl">Highlight your support center or team.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="portal-slide" data-portal-slide>
                    <div class="relative h-full w-full">
                        <div class="h-full w-full bg-cover bg-center" style="background-image: url('{{ asset('images/home/lab.png') }}');"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/40 to-transparent"></div>
                        <div class="absolute inset-0 flex items-end">
                            <div class="p-6 md:p-8 space-y-1 text-white">
                                <h3 class="text-lg md:text-xl font-bold">Events & Visits</h3>
                                <p class="text-sm md:text-base text-slate-100/90 max-w-xl">Add photos from open days or campus tours.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="absolute inset-x-0 bottom-0 flex items-center justify-between px-4 pb-4">
                <div class="flex gap-2">
                    <button type="button" class="rounded-full bg-black/40 p-2 text-white hover:bg-black/70 transition" data-portal-slider-prev aria-label="Previous slide">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button type="button" class="rounded-full bg-black/40 p-2 text-white hover:bg-black/70 transition" data-portal-slider-next aria-label="Next slide">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" class="portal-slider-dot h-2.5 w-2.5 rounded-full bg-white/80 opacity-50" data-portal-slider-dot aria-label="Go to slide 1"></button>
                    <button type="button" class="portal-slider-dot h-2.5 w-2.5 rounded-full bg-white/80 opacity-50" data-portal-slider-dot aria-label="Go to slide 2"></button>
                    <button type="button" class="portal-slider-dot h-2.5 w-2.5 rounded-full bg-white/80 opacity-50" data-portal-slider-dot aria-label="Go to slide 3"></button>
                </div>
            </div>
        </div>
    </section>

    {{-- Contact Information Cards --}}
    <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="contact-card group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:shadow-2xl transition-all duration-300 hover:-translate-y-2">
            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-blue-500/10 to-transparent rounded-full blur-2xl"></div>
            <div class="relative z-10">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 text-white mb-4 shadow-lg group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Email Us</h3>
                <p class="text-sm text-slate-600 mb-2">Send us an email anytime</p>
                <div class="text-sm font-bold text-[color:var(--portal-navy)] mb-2">
                    {{ ($stats['totalStudents'] + $stats['totalFaculty']) > 0 ? number_format($stats['totalStudents'] + $stats['totalFaculty']) . '+' : '0' }} Users
                </div>
                <a href="mailto:info@university.edu" class="text-base font-semibold text-[color:var(--portal-navy)] hover:text-blue-600 transition-colors">
                    info@university.edu
                </a>
            </div>
        </div>
        
        <div class="contact-card group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:shadow-2xl transition-all duration-300 hover:-translate-y-2">
            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-green-500/10 to-transparent rounded-full blur-2xl"></div>
            <div class="relative z-10">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-green-500 to-green-600 text-white mb-4 shadow-lg group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Call Us</h3>
                <p class="text-sm text-slate-600 mb-2">Mon-Fri, 9am-5pm</p>
                <div class="text-sm font-bold text-[color:var(--portal-navy)] mb-2">
                    {{ $stats['totalFaculty'] > 0 ? number_format($stats['totalFaculty']) : '0' }} Faculty Members
                </div>
                <a href="tel:+1234567890" class="text-base font-semibold text-[color:var(--portal-navy)] hover:text-green-600 transition-colors">
                    +123 456 7890
                </a>
            </div>
        </div>
        
        <div class="contact-card group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:shadow-2xl transition-all duration-300 hover:-translate-y-2">
            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-purple-500/10 to-transparent rounded-full blur-2xl"></div>
            <div class="relative z-10">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-purple-500 to-purple-600 text-white mb-4 shadow-lg group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Visit Us</h3>
                <p class="text-sm text-slate-600 mb-2">Come see our campus</p>
                <div class="text-sm font-bold text-[color:var(--portal-navy)] mb-2">
                    {{ $stats['totalCourses'] > 0 ? number_format($stats['totalCourses']) : '0' }} Courses Offered
                </div>
                <p class="text-base font-semibold text-[color:var(--portal-navy)]">
                    123 University Avenue<br>
                    City, Country
                </p>
            </div>
        </div>
        
        <div class="contact-card group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:shadow-2xl transition-all duration-300 hover:-translate-y-2">
            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-amber-500/10 to-transparent rounded-full blur-2xl"></div>
            <div class="relative z-10">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-amber-500 to-amber-600 text-white mb-4 shadow-lg group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Office Hours</h3>
                <p class="text-sm text-slate-600 mb-3">When we're available</p>
                <p class="text-base font-semibold text-[color:var(--portal-navy)]">
                    Mon-Fri: 9am-5pm<br>
                    Sat-Sun: Closed
                </p>
            </div>
        </div>
    </section>

    {{-- Contact Form & Map Section --}}
    <section class="grid gap-8 lg:grid-cols-2">
        {{-- Contact Form --}}
        <div class="space-y-6">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-[color:var(--portal-navy)] mb-2">Send a Message</p>
                <h2 class="text-3xl md:text-4xl font-bold text-[color:var(--portal-navy)] mb-4">Get in Touch</h2>
                <p class="text-slate-600">Fill out the form below and we'll respond as soon as possible.</p>
            </div>
            
            @if (session('success'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">
                    <div class="font-semibold">Message sent</div>
                    <div class="text-sm">{{ session('success') }}</div>
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800">
                    <div class="font-semibold">Please fix the errors below</div>
                    <ul class="mt-2 list-disc pl-5 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('guest.contact.store') }}" class="space-y-6 rounded-2xl border border-slate-200 bg-white p-6 md:p-8 shadow-lg" data-recaptcha-action="contact">
                @csrf
                @if (config('recaptcha.site_key'))
                    <input type="hidden" name="recaptcha_token" value="">
                @endif
                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <label for="firstName" class="block text-sm font-semibold text-slate-700 mb-2">
                            First Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="firstName"
                            name="firstName"
                            required
                            class="form-input w-full px-4 py-3 rounded-xl border border-slate-300 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[color:var(--portal-navy)] focus:border-transparent transition-all"
                            placeholder="John"
                            value="{{ old('firstName') }}"
                        >
                    </div>
                    <div>
                        <label for="lastName" class="block text-sm font-semibold text-slate-700 mb-2">
                            Last Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="lastName"
                            name="lastName"
                            required
                            class="form-input w-full px-4 py-3 rounded-xl border border-slate-300 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[color:var(--portal-navy)] focus:border-transparent transition-all"
                            placeholder="Doe"
                            value="{{ old('lastName') }}"
                        >
                    </div>
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        required
                        class="form-input w-full px-4 py-3 rounded-xl border border-slate-300 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[color:var(--portal-navy)] focus:border-transparent transition-all"
                        placeholder="john.doe@example.com"
                        value="{{ old('email') }}"
                    >
                </div>
                
                <div>
                    <label for="phone" class="block text-sm font-semibold text-slate-700 mb-2">
                        Phone Number
                    </label>
                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        class="form-input w-full px-4 py-3 rounded-xl border border-slate-300 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[color:var(--portal-navy)] focus:border-transparent transition-all"
                        placeholder="+123 456 7890"
                        value="{{ old('phone') }}"
                    >
                </div>
                
                <div>
                    <label for="subject" class="block text-sm font-semibold text-slate-700 mb-2">
                        Subject <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="subject"
                        name="subject"
                        required
                        class="form-input w-full px-4 py-3 rounded-xl border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-[color:var(--portal-navy)] focus:border-transparent transition-all cursor-pointer"
                    >
                        <option value="">Select a subject</option>
                        <option value="admissions" @selected(old('subject') === 'admissions')>Admissions Inquiry</option>
                        <option value="academic" @selected(old('subject') === 'academic')>Academic Questions</option>
                        <option value="financial" @selected(old('subject') === 'financial')>Financial Aid</option>
                        <option value="general" @selected(old('subject') === 'general')>General Inquiry</option>
                        <option value="other" @selected(old('subject') === 'other')>Other</option>
                    </select>
                </div>
                
                <div>
                    <label for="message" class="block text-sm font-semibold text-slate-700 mb-2">
                        Message <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        id="message"
                        name="message"
                        rows="5"
                        required
                        class="form-input w-full px-4 py-3 rounded-xl border border-slate-300 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[color:var(--portal-navy)] focus:border-transparent transition-all resize-none"
                        placeholder="Tell us how we can help you..."
                    >{{ old('message') }}</textarea>
                </div>
                
                <button
                    type="submit"
                    class="w-full rounded-xl bg-gradient-to-r from-[color:var(--portal-navy)] to-slate-800 px-6 py-4 text-base font-semibold text-white shadow-lg hover:shadow-xl hover:scale-[1.02] transition-all duration-300"
                >
                    <span class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        Send Message
                    </span>
                </button>
            </form>
        </div>
        
        {{-- Map Section --}}
        <div class="space-y-6">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-[color:var(--portal-navy)] mb-2">Find Us</p>
                <h2 class="text-3xl md:text-4xl font-bold text-[color:var(--portal-navy)] mb-4">Our Location</h2>
                <p class="text-slate-600">Visit our campus or get directions using the map below.</p>
            </div>
            
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-lg">
                <div class="overflow-hidden rounded-xl border border-slate-200 mb-6">
                    <iframe
                        class="h-64 w-full md:h-80"
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3151.8354345093746!2d144.9537363153167!3d-37.81627974202137!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ad65d43f1f1f1f1%3A0xf1f1f1f1f1f1f1f1!2sUniversity!5e0!3m2!1sen!2s!4v1610000000000!5m2!1sen!2s"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                    ></iframe>
                </div>
                
                <div class="space-y-4">
                    <div class="flex items-start gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-[color:var(--portal-navy)] to-slate-700 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-900 mb-1">Main Campus</h3>
                            <p class="text-sm text-slate-600">123 University Avenue, City, Country</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-[color:var(--portal-gold)] to-amber-400 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-900 mb-1">Parking Available</h3>
                            <p class="text-sm text-slate-600">Free parking for visitors in designated areas</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Departments & Quick Links --}}
    <section class="space-y-8">
        <div class="text-center max-w-3xl mx-auto">
            <p class="text-xs font-semibold uppercase tracking-wide text-[color:var(--portal-navy)] mb-3">Need Specific Help?</p>
            <h2 class="text-3xl md:text-4xl font-bold text-[color:var(--portal-navy)] mb-4">Departments & Services</h2>
            <p class="text-slate-600">Connect with the right department for faster assistance</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">Admissions</h3>
                        <p class="text-sm text-slate-600 mb-3">Questions about enrollment and applications</p>
                        <a href="mailto:admissions@university.edu" class="text-sm font-semibold text-[color:var(--portal-navy)] hover:text-blue-600 transition-colors">
                            admissions@university.edu
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">Financial Aid</h3>
                        <p class="text-sm text-slate-600 mb-3">Scholarships, grants, and financial support</p>
                        <a href="mailto:financialaid@university.edu" class="text-sm font-semibold text-[color:var(--portal-navy)] hover:text-green-600 transition-colors">
                            financialaid@university.edu
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">Academic Affairs</h3>
                        <p class="text-sm text-slate-600 mb-3">Course information and academic programs</p>
                        <a href="mailto:academics@university.edu" class="text-sm font-semibold text-[color:var(--portal-navy)] hover:text-purple-600 transition-colors">
                            academics@university.edu
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">IT Support</h3>
                        <p class="text-sm text-slate-600 mb-3">Technical assistance and portal help</p>
                        <a href="mailto:itsupport@university.edu" class="text-sm font-semibold text-[color:var(--portal-navy)] hover:text-amber-600 transition-colors">
                            itsupport@university.edu
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">Registrar</h3>
                        <p class="text-sm text-slate-600 mb-3">Records, transcripts, and enrollment</p>
                        <a href="mailto:registrar@university.edu" class="text-sm font-semibold text-[color:var(--portal-navy)] hover:text-red-600 transition-colors">
                            registrar@university.edu
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">Student Services</h3>
                        <p class="text-sm text-slate-600 mb-3">Support and resources for students</p>
                        <a href="mailto:studentservices@university.edu" class="text-sm font-semibold text-[color:var(--portal-navy)] hover:text-indigo-600 transition-colors">
                            studentservices@university.edu
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Social Media Section --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-50 via-white to-slate-100 border-2 border-slate-200 p-8 md:p-12 shadow-xl">
        <div class="text-center mb-8">
            <p class="text-xs font-semibold uppercase tracking-wide text-[color:var(--portal-navy)] mb-3">Stay Connected</p>
            <h2 class="text-3xl md:text-4xl font-bold text-[color:var(--portal-navy)] mb-4">Follow Us</h2>
            <p class="text-slate-600">Connect with us on social media for updates, news, and events</p>
        </div>
        
        <div class="flex flex-wrap items-center justify-center gap-4">
            <a href="#" class="group flex items-center gap-3 rounded-2xl bg-white border-2 border-slate-200 px-6 py-4 shadow-md hover:shadow-xl hover:border-blue-500 transition-all duration-300 hover:-translate-y-1" aria-label="Facebook">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-slate-900">Facebook</p>
                    <p class="text-xs text-slate-500">@university</p>
                </div>
            </a>
            
            <a href="#" class="group flex items-center gap-3 rounded-2xl bg-white border-2 border-slate-200 px-6 py-4 shadow-md hover:shadow-xl hover:border-sky-500 transition-all duration-300 hover:-translate-y-1" aria-label="Twitter">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-sky-500 to-sky-600 flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-slate-900">Twitter</p>
                    <p class="text-xs text-slate-500">@university</p>
                </div>
            </a>
            
            <a href="#" class="group flex items-center gap-3 rounded-2xl bg-white border-2 border-slate-200 px-6 py-4 shadow-md hover:shadow-xl hover:border-pink-500 transition-all duration-300 hover:-translate-y-1" aria-label="Instagram">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-pink-500 to-pink-600 flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-slate-900">Instagram</p>
                    <p class="text-xs text-slate-500">@university</p>
                </div>
            </a>
            
            <a href="#" class="group flex items-center gap-3 rounded-2xl bg-white border-2 border-slate-200 px-6 py-4 shadow-md hover:shadow-xl hover:border-blue-700 transition-all duration-300 hover:-translate-y-1" aria-label="LinkedIn">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-700 to-blue-800 flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-slate-900">LinkedIn</p>
                    <p class="text-xs text-slate-500">@university</p>
                </div>
            </a>
        </div>
    </section>
</div>
@endsection

