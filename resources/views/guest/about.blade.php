@extends('layouts.guest')

@section('title', 'About Us')

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
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.8s ease-out;
    }
    .animate-float {
        animation: float 6s ease-in-out infinite;
    }
    .value-card:nth-child(1) { animation-delay: 0.1s; }
    .value-card:nth-child(2) { animation-delay: 0.2s; }
    .value-card:nth-child(3) { animation-delay: 0.3s; }
    .value-card:nth-child(4) { animation-delay: 0.4s; }
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Our Story
            </p>
            <h1 class="text-5xl md:text-6xl lg:text-7xl font-bold leading-tight">
                Shaping Futures Through
                <span class="block text-transparent bg-clip-text bg-gradient-to-r from-[color:var(--portal-gold)] to-amber-300 mt-2">
                    Excellence in Education
                </span>
            </h1>
            <p class="text-xl md:text-2xl text-slate-200 max-w-3xl mx-auto leading-relaxed">
                We are committed to academic excellence, innovation, and student success. Our mission is to provide quality education and foster a vibrant learning community that prepares students for global challenges.
            </p>
            <div class="flex flex-wrap items-center justify-center gap-4 pt-4">
                <a href="{{ route('guest.courses') }}" class="inline-flex items-center gap-2 rounded-full bg-white px-8 py-4 text-base font-semibold text-[color:var(--portal-navy)] shadow-lg hover:bg-slate-100 hover:scale-105 transition-all duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    Explore Courses
                </a>
                <a href="{{ route('guest.contact') }}" class="inline-flex items-center gap-2 rounded-full border-2 border-white/30 backdrop-blur-sm bg-white/10 px-8 py-4 text-base font-semibold text-white hover:bg-white/20 transition-all duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Contact Us
                </a>
            </div>
        </div>
    </section>

    {{-- About Gallery Slider --}}
    <section class="space-y-4">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-[color:var(--portal-navy)] mb-1">Our Campus & People</p>
                <h2 class="text-2xl md:text-3xl font-bold text-[color:var(--portal-navy)]">See Our Story</h2>
                <p class="text-sm md:text-base text-slate-600">Add images of campus life, leadership, and milestones here.</p>
            </div>
        </div>

        <div class="portal-slider rounded-3xl border border-slate-200 bg-slate-900/90 shadow-xl overflow-hidden" data-portal-slider data-autoplay="true" data-interval="6000">
            <div class="portal-slider-track relative">
                <div class="portal-slide is-active" data-portal-slide>
                    <div class="absolute inset-0 overflow-hidden">
                        <div class="portal-slide-image" style="background-image: url('{{ asset('images/about/campuslife.jpg') }}'); background-size: cover; background-position: center;"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/40 to-transparent"></div>
                        <div class="absolute inset-0 flex items-end">
                            <div class="p-6 md:p-8 space-y-1 text-white">
                                <h3 class="text-lg md:text-xl font-bold">Campus Life</h3>
                                <p class="text-sm md:text-base text-slate-100/90 max-w-xl">Everyday moments, campus culture, and the community that makes our university unique.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="portal-slide" data-portal-slide>
                    <div class="absolute inset-0 overflow-hidden">
                        <div class="portal-slide-image" style="background-image: url('{{ asset('images/about/leadership.jpg') }}'); background-size: cover; background-position: center;"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/40 to-transparent"></div>
                        <div class="absolute inset-0 flex items-end">
                            <div class="p-6 md:p-8 space-y-1 text-white">
                                <h3 class="text-lg md:text-xl font-bold">Leadership & Vision</h3>
                                <p class="text-sm md:text-base text-slate-100/90 max-w-xl">Meet the leaders, faculty, and visionaries guiding our institution and shaping the future.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="portal-slide" data-portal-slide>
                    <div class="absolute inset-0 overflow-hidden">
                        <div class="portal-slide-image" style="background-image: url('{{ asset('images/about/achievement.jpg') }}'); background-size: cover; background-position: center;"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/40 to-transparent"></div>
                        <div class="absolute inset-0 flex items-end">
                            <div class="p-6 md:p-8 space-y-1 text-white">
                                <h3 class="text-lg md:text-xl font-bold">Milestones & Achievements</h3>
                                <p class="text-sm md:text-base text-slate-100/90 max-w-xl">Awards, accreditations, and milestones that reflect our commitment to excellence.</p>
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

    {{-- Quick links strip (consistent with Home) --}}
    @include('guest.partials.quick-links-strip')

    {{-- Page-specific image + text feature cards (3 cards) --}}
    @include('guest.partials.image-cards-about')

    {{-- Mission & Vision --}}
    <section class="grid gap-8 lg:grid-cols-2">
        <div class="group relative overflow-hidden rounded-3xl border-2 border-slate-200 bg-gradient-to-br from-white to-slate-50 p-8 md:p-10 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-[color:var(--portal-navy)]/5 to-transparent rounded-full blur-2xl"></div>
            <div class="relative z-10">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-[color:var(--portal-navy)] to-slate-700 text-white mb-6 shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 class="text-3xl md:text-4xl font-bold text-[color:var(--portal-navy)] mb-4">Our Mission</h2>
                <p class="text-lg text-slate-700 leading-relaxed mb-6">
                    Empower learners to achieve their potential through research-driven teaching and inclusive academic support.
                </p>
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-[color:var(--portal-gold)] mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <p class="text-slate-700">Foster critical thinking and innovation</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-[color:var(--portal-gold)] mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <p class="text-slate-700">Provide accessible, quality education for all</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-[color:var(--portal-gold)] mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <p class="text-slate-700">Build a supportive learning community</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="group relative overflow-hidden rounded-3xl border-2 border-slate-200 bg-gradient-to-br from-white to-slate-50 p-8 md:p-10 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-[color:var(--portal-gold)]/10 to-transparent rounded-full blur-2xl"></div>
            <div class="relative z-10">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-[color:var(--portal-gold)] to-amber-400 text-white mb-6 shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </div>
                <h2 class="text-3xl md:text-4xl font-bold text-[color:var(--portal-navy)] mb-4">Our Vision</h2>
                <p class="text-lg text-slate-700 leading-relaxed mb-6">
                    To be a leader in academic excellence and research, preparing graduates to create positive impact globally.
                </p>
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-[color:var(--portal-gold)] mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <p class="text-slate-700">Global recognition for academic excellence</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-[color:var(--portal-gold)] mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <p class="text-slate-700">Cutting-edge research and innovation</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-[color:var(--portal-gold)] mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <p class="text-slate-700">Graduates making a difference worldwide</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Core Values --}}
    <section class="space-y-8">
        <div class="text-center max-w-3xl mx-auto">
            <p class="text-xs font-semibold uppercase tracking-wide text-[color:var(--portal-navy)] mb-3">What We Stand For</p>
            <h2 class="text-4xl md:text-5xl font-bold text-[color:var(--portal-navy)] mb-4">Our Core Values</h2>
            <p class="text-lg text-slate-600">The principles that guide everything we do</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="value-card group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 animate-fade-in-up">
                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-blue-500/10 to-transparent rounded-full blur-2xl"></div>
                <div class="relative z-10">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 text-white mb-4 shadow-lg group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Excellence</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">Striving for the highest standards in teaching, research, and student support.</p>
                </div>
            </div>
            
            <div class="value-card group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 animate-fade-in-up">
                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-green-500/10 to-transparent rounded-full blur-2xl"></div>
                <div class="relative z-10">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-xl bg-gradient-to-br from-green-500 to-green-600 text-white mb-4 shadow-lg group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Inclusivity</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">Creating an environment where everyone can thrive and succeed.</p>
                </div>
            </div>
            
            <div class="value-card group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 animate-fade-in-up">
                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-purple-500/10 to-transparent rounded-full blur-2xl"></div>
                <div class="relative z-10">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 text-white mb-4 shadow-lg group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Innovation</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">Embracing new ideas and technologies to enhance learning experiences.</p>
                </div>
            </div>
            
            <div class="value-card group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 animate-fade-in-up">
                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-amber-500/10 to-transparent rounded-full blur-2xl"></div>
                <div class="relative z-10">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 text-white mb-4 shadow-lg group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Integrity</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">Maintaining the highest ethical standards in all our endeavors.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Statistics --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[color:var(--portal-navy)] via-slate-800 to-[color:var(--portal-navy)] p-8 md:p-12 text-white shadow-2xl">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 right-0 w-96 h-96 bg-[color:var(--portal-gold)] rounded-full mix-blend-multiply filter blur-3xl"></div>
        </div>
        
        <div class="relative z-10">
            <div class="text-center mb-12">
                <p class="text-xs font-semibold uppercase tracking-wide text-amber-200 mb-3">By The Numbers</p>
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Our Impact</h2>
                <p class="text-slate-300 max-w-2xl mx-auto">Measurable achievements that reflect our commitment to excellence</p>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-8">
                <div class="text-center">
                    <div class="text-4xl md:text-5xl lg:text-6xl font-bold text-[color:var(--portal-gold)] mb-2">
                        {{ $stats['yearsOfExcellence'] > 0 ? number_format($stats['yearsOfExcellence']) . '+' : '50+' }}
                    </div>
                    <div class="text-sm md:text-base text-slate-300 uppercase tracking-wide">Years of Excellence</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl md:text-5xl lg:text-6xl font-bold text-[color:var(--portal-gold)] mb-2">
                        {{ $stats['totalStudents'] > 0 ? number_format($stats['totalStudents']) . '+' : '5,000+' }}
                    </div>
                    <div class="text-sm md:text-base text-slate-300 uppercase tracking-wide">Active Students</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl md:text-5xl lg:text-6xl font-bold text-[color:var(--portal-gold)] mb-2">
                        {{ $stats['totalFaculty'] > 0 ? number_format($stats['totalFaculty']) . '+' : '200+' }}
                    </div>
                    <div class="text-sm md:text-base text-slate-300 uppercase tracking-wide">Expert Faculty</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl md:text-5xl lg:text-6xl font-bold text-[color:var(--portal-gold)] mb-2">
                        {{ $stats['totalPrograms'] > 0 ? number_format($stats['totalPrograms']) . '+' : '100+' }}
                    </div>
                    <div class="text-sm md:text-base text-slate-300 uppercase tracking-wide">Programs Offered</div>
                </div>
            </div>
        </div>
    </section>

    {{-- Timeline/History --}}
    <section class="space-y-8">
        <div class="text-center max-w-3xl mx-auto">
            <p class="text-xs font-semibold uppercase tracking-wide text-[color:var(--portal-navy)] mb-3">Our Journey</p>
            <h2 class="text-4xl md:text-5xl font-bold text-[color:var(--portal-navy)] mb-4">Milestones & Achievements</h2>
            <p class="text-lg text-slate-600">Key moments in our history of growth and innovation</p>
        </div>
        
        <div class="relative">
            {{-- Timeline Line --}}
            <div class="absolute left-8 md:left-1/2 top-0 bottom-0 w-0.5 bg-gradient-to-b from-[color:var(--portal-navy)] via-[color:var(--portal-gold)] to-[color:var(--portal-navy)] transform md:-translate-x-1/2"></div>
            
            <div class="space-y-12">
                @php
                    $milestones = [
                        ['year' => '1975', 'title' => 'Foundation', 'description' => 'University established with a vision to provide quality education'],
                        ['year' => '1990', 'title' => 'Expansion', 'description' => 'Opened new campuses and introduced graduate programs'],
                        ['year' => '2005', 'title' => 'Digital Transformation', 'description' => 'Launched online learning platform and digital resources'],
                        ['year' => '2020', 'title' => 'Global Recognition', 'description' => 'Achieved international accreditation and partnerships'],
                    ];
                @endphp
                
                @foreach($milestones as $index => $milestone)
                    <div class="relative flex items-center gap-6 md:gap-8">
                        {{-- Timeline Dot --}}
                        <div class="flex-shrink-0 relative z-10">
                            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-[color:var(--portal-navy)] to-slate-700 border-4 border-white shadow-xl flex items-center justify-center text-white font-bold text-sm">
                                {{ $milestone['year'] }}
                            </div>
                        </div>
                        
                        {{-- Content Card --}}
                        <div class="flex-1 {{ $index % 2 === 0 ? 'md:pr-8' : 'md:pl-8 md:ml-auto md:w-1/2' }}">
                            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:shadow-xl transition-all">
                                <h3 class="text-xl font-bold text-[color:var(--portal-navy)] mb-2">{{ $milestone['title'] }}</h3>
                                <p class="text-slate-600">{{ $milestone['description'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Campus Highlights --}}
    <section class="space-y-8">
        <div class="text-center max-w-3xl mx-auto">
            <p class="text-xs font-semibold uppercase tracking-wide text-[color:var(--portal-navy)] mb-3">Our Facilities</p>
            <h2 class="text-4xl md:text-5xl font-bold text-[color:var(--portal-navy)] mb-4">Campus Highlights</h2>
            <p class="text-lg text-slate-600">State-of-the-art facilities designed to enhance your learning experience</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-md hover:shadow-2xl transition-all duration-300 hover:-translate-y-2">
                <div class="h-48 bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                    <svg class="w-20 h-20 text-white group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Modern Libraries</h3>
                    <p class="text-slate-600">Extensive collections and digital resources for research and study.</p>
                </div>
            </div>
            
            <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-md hover:shadow-2xl transition-all duration-300 hover:-translate-y-2">
                <div class="h-48 bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center">
                    <svg class="w-20 h-20 text-white group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Research Labs</h3>
                    <p class="text-slate-600">Cutting-edge laboratories equipped with the latest technology.</p>
                </div>
            </div>
            
            <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-md hover:shadow-2xl transition-all duration-300 hover:-translate-y-2">
                <div class="h-48 bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center">
                    <svg class="w-20 h-20 text-white group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Student Centers</h3>
                    <p class="text-slate-600">Vibrant spaces for collaboration, events, and student activities.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Call to Action --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[color:var(--portal-gold)] via-amber-400 to-[color:var(--portal-gold)] p-8 md:p-12 text-[color:var(--portal-navy)] shadow-2xl">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 right-0 w-96 h-96 bg-[color:var(--portal-navy)] rounded-full mix-blend-multiply filter blur-3xl"></div>
        </div>
        
        <div class="relative z-10 max-w-3xl mx-auto text-center space-y-6">
            <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-4">Ready to Start Your Journey?</h2>
            <p class="text-lg md:text-xl text-slate-800 leading-relaxed">
                Join our community of learners and discover endless possibilities for your future.
            </p>
            <div class="flex flex-wrap items-center justify-center gap-4 pt-4">
                <a href="{{ route('guest.courses') }}" class="inline-flex items-center gap-2 rounded-full bg-[color:var(--portal-navy)] px-8 py-4 text-base font-semibold text-white shadow-lg hover:bg-slate-800 hover:scale-105 transition-all duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    Browse Courses
                </a>
                <a href="{{ route('guest.contact') }}" class="inline-flex items-center gap-2 rounded-full border-2 border-[color:var(--portal-navy)] bg-white/90 backdrop-blur-sm px-8 py-4 text-base font-semibold text-[color:var(--portal-navy)] hover:bg-white transition-all duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Get in Touch
                </a>
            </div>
        </div>
    </section>
</div>
@endsection

