@extends('layouts.guest')

@section('title', 'Welcome to University Portal')

@push('styles')
<style>
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
    }
    @keyframes pulse-glow {
        0%, 100% { opacity: 0.5; }
        50% { opacity: 1; }
    }
    .animate-float {
        animation: float 6s ease-in-out infinite;
    }
    .animate-pulse-glow {
        animation: pulse-glow 3s ease-in-out infinite;
    }
    .gradient-mask {
        mask-image: linear-gradient(to bottom, black 60%, transparent 100%);
        -webkit-mask-image: linear-gradient(to bottom, black 60%, transparent 100%);
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6 space-y-16">
    {{-- Enhanced Hero --}}
    <section class="relative overflow-hidden rounded-3xl border border-slate-200 bg-gradient-to-br from-[color:var(--portal-navy)] via-slate-900 to-[color:var(--portal-navy)] px-8 py-16 lg:py-24 text-white shadow-2xl">
        {{-- Animated Background Elements --}}
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-10 left-10 w-72 h-72 bg-[color:var(--portal-gold)] rounded-full mix-blend-multiply filter blur-3xl animate-pulse-glow"></div>
            <div class="absolute top-40 right-20 w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl animate-pulse-glow" style="animation-delay: 1s;"></div>
            <div class="absolute -bottom-20 left-1/2 w-80 h-80 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl animate-pulse-glow" style="animation-delay: 2s;"></div>
        </div>
        
        {{-- Decorative Grid Pattern --}}
        <div class="absolute inset-0 opacity-10" style="background-image: linear-gradient(rgba(255,255,255,0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.1) 1px, transparent 1px); background-size: 50px 50px;"></div>
        
        <div class="relative z-10 max-w-4xl mx-auto text-center space-y-6">
            <p class="inline-flex items-center gap-2 rounded-full bg-white/10 backdrop-blur-sm px-4 py-2 text-xs font-semibold uppercase tracking-wide text-amber-200 ring-1 ring-white/20 animate-float">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                </svg>
                Welcome to Excellence in Education
            </p>
            <h1 class="text-5xl md:text-6xl lg:text-7xl font-bold leading-tight">
                Transform Your Future
                <span class="block text-transparent bg-clip-text bg-gradient-to-r from-[color:var(--portal-gold)] to-amber-300 mt-2">
                    With Quality Education
                </span>
            </h1>
            <p class="text-xl md:text-2xl text-slate-200 max-w-2xl mx-auto leading-relaxed">
                Explore our comprehensive courses, vibrant campus life, and cutting-edge facilities. Join thousands of students shaping their future.
            </p>
            <div class="flex flex-wrap items-center justify-center gap-4 pt-4">
                <a href="{{ route('login') }}" class="group relative inline-flex items-center gap-2 rounded-full bg-white px-8 py-4 text-base font-semibold text-[color:var(--portal-navy)] shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    Get Started
                </a>
                @if (Route::has('register'))
                <a href="{{ route('register') }}" class="group inline-flex items-center gap-2 rounded-full border-2 border-white/30 backdrop-blur-sm bg-white/10 px-8 py-4 text-base font-semibold text-white hover:bg-white/20 hover:border-white/50 transition-all duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    Create Account
                </a>
                @endif
            </div>
            
            {{-- Quick Stats in Hero --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 pt-12 mt-12 border-t border-white/20">
                <div class="text-center">
                    <div class="text-3xl md:text-4xl font-bold text-[color:var(--portal-gold)]">
                        {{ $stats['totalCourses'] > 0 ? number_format($stats['totalCourses']) . '+' : '100+' }}
                    </div>
                    <div class="text-sm text-slate-300 mt-1">Courses</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl md:text-4xl font-bold text-[color:var(--portal-gold)]">
                        @if($stats['totalStudents'] >= 1000)
                            {{ number_format($stats['totalStudents'] / 1000, 1) }}K+
                        @elseif($stats['totalStudents'] > 0)
                            {{ number_format($stats['totalStudents']) }}+
                        @else
                            5K+
                        @endif
                    </div>
                    <div class="text-sm text-slate-300 mt-1">Students</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl md:text-4xl font-bold text-[color:var(--portal-gold)]">
                        {{ $stats['totalFaculty'] > 0 ? number_format($stats['totalFaculty']) . '+' : '200+' }}
                    </div>
                    <div class="text-sm text-slate-300 mt-1">Faculty</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl md:text-4xl font-bold text-[color:var(--portal-gold)]">{{ $stats['successRate'] }}%</div>
                    <div class="text-sm text-slate-300 mt-1">Success Rate</div>
                </div>
            </div>
        </div>
    </section>

    {{-- Image Slider (Hero Highlights) --}}
    <section class="space-y-4">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-[color:var(--portal-navy)] mb-1">Campus Highlights</p>
                <h2 class="text-2xl md:text-3xl font-bold text-[color:var(--portal-navy)]">Explore Our University</h2>
                <p class="text-sm md:text-base text-slate-600">
                    Add your own campus, classroom, or event photos here to visually introduce the university.
                </p>
            </div>
        </div>

        <div
            class="portal-slider rounded-3xl border border-slate-200 bg-slate-900/90 shadow-xl overflow-hidden"
            data-portal-slider
            data-autoplay="true"
            data-interval="6000"
        >
            <div class="portal-slider-track relative">
                {{-- Slide 1 --}}
                <div
                    class="portal-slide is-active"
                    data-portal-slide
                >
                    <div class="absolute inset-0 overflow-hidden">
                        <div
                            class="portal-slide-image"
                            style="background-image: url('{{ asset('images/home/slide1.png') }}');"
                        ></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/40 to-transparent"></div>
                        <div class="absolute inset-0 flex items-end">
                            <div class="p-6 md:p-8 space-y-2 text-white">
                                <h3 class="text-xl md:text-2xl font-bold">Modern Learning Environment</h3>
                                <p class="text-sm md:text-base text-slate-100/90 max-w-xl">
                                    Showcase your main campus or learning spaces to welcome students visually.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Slide 2 --}}
                <div
                    class="portal-slide"
                    data-portal-slide
                >
                    <div class="absolute inset-0 overflow-hidden">
                        <div
                            class="portal-slide-image"
                            style="background-image: url('{{ asset('images/home/slide2.png') }}');"
                        ></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/40 to-transparent"></div>
                        <div class="absolute inset-0 flex items-end">
                            <div class="p-6 md:p-8 space-y-2 text-white">
                                <h3 class="text-xl md:text-2xl font-bold">Student Life & Activities</h3>
                                <p class="text-sm md:text-base text-slate-100/90 max-w-xl">
                                    Highlight events, clubs, or student activities that reflect your campus culture.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Slide 3 --}}
                <div
                    class="portal-slide"
                    data-portal-slide
                >
                    <div class="absolute inset-0 overflow-hidden">
                        <div
                            class="portal-slide-image"
                            style="background-image: url('{{ asset('images/home/slide3.png') }}');"
                        ></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/40 to-transparent"></div>
                        <div class="absolute inset-0 flex items-end">
                            <div class="p-6 md:p-8 space-y-2 text-white">
                                <h3 class="text-xl md:text-2xl font-bold">Labs & Facilities</h3>
                                <p class="text-sm md:text-base text-slate-100/90 max-w-xl">
                                    Use this slide to show laboratories, libraries, or any key facilities.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Slider Controls --}}
            <div class="absolute inset-x-0 bottom-0 flex items-center justify-between px-4 pb-4">
                <div class="flex gap-2">
                    <button
                        type="button"
                        class="rounded-full bg-black/40 p-2 text-white hover:bg-black/70 transition"
                        data-portal-slider-prev
                        aria-label="Previous slide"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    <button
                        type="button"
                        class="rounded-full bg-black/40 p-2 text-white hover:bg-black/70 transition"
                        data-portal-slider-next
                        aria-label="Next slide"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        class="portal-slider-dot h-2.5 w-2.5 rounded-full bg-white/80 opacity-50"
                        data-portal-slider-dot
                        aria-label="Go to slide 1"
                    ></button>
                    <button
                        type="button"
                        class="portal-slider-dot h-2.5 w-2.5 rounded-full bg-white/80 opacity-50"
                        data-portal-slider-dot
                        aria-label="Go to slide 2"
                    ></button>
                    <button
                        type="button"
                        class="portal-slider-dot h-2.5 w-2.5 rounded-full bg-white/80 opacity-50"
                        data-portal-slider-dot
                        aria-label="Go to slide 3"
                    ></button>
                </div>
            </div>
        </div>
    </section>

    {{-- Statistics Section --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-50 via-white to-slate-100 border border-slate-200 p-8 md:p-12 shadow-lg">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-8">
            <div class="text-center group">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-[color:var(--portal-navy)] to-slate-700 text-white mb-4 shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <div class="text-4xl md:text-5xl font-bold text-[color:var(--portal-navy)] mb-2">
                    {{ $stats['totalCourses'] > 0 ? number_format($stats['totalCourses']) . '+' : '100+' }}
                </div>
                <div class="text-sm font-semibold text-slate-600 uppercase tracking-wide">Active Courses</div>
            </div>
            <div class="text-center group">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-[color:var(--portal-gold)] to-amber-400 text-white mb-4 shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div class="text-4xl md:text-5xl font-bold text-[color:var(--portal-navy)] mb-2">
                    @if($stats['totalStudents'] >= 1000)
                        {{ number_format($stats['totalStudents'] / 1000, 1) }}K+
                    @elseif($stats['totalStudents'] > 0)
                        {{ number_format($stats['totalStudents']) }}+
                    @else
                        5,000+
                    @endif
                </div>
                <div class="text-sm font-semibold text-slate-600 uppercase tracking-wide">Students</div>
            </div>
            <div class="text-center group">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 text-white mb-4 shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="text-4xl md:text-5xl font-bold text-[color:var(--portal-navy)] mb-2">
                    {{ $stats['totalFaculty'] > 0 ? number_format($stats['totalFaculty']) . '+' : '200+' }}
                </div>
                <div class="text-sm font-semibold text-slate-600 uppercase tracking-wide">Expert Faculty</div>
            </div>
            <div class="text-center group">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-green-500 to-green-600 text-white mb-4 shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
                <div class="text-4xl md:text-5xl font-bold text-[color:var(--portal-navy)] mb-2">{{ $stats['successRate'] }}%</div>
                <div class="text-sm font-semibold text-slate-600 uppercase tracking-wide">Success Rate</div>
            </div>
        </div>
    </section>

    {{-- About + Location --}}
    <section class="grid gap-8 lg:grid-cols-2">
        <div class="space-y-6">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-[color:var(--portal-navy)] to-slate-700 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-[color:var(--portal-navy)]">About the University</h2>
            </div>
            <p class="text-base text-slate-700 leading-relaxed">
                Our mission is to provide quality education, foster innovation, and prepare students for a successful future. We are committed to excellence in teaching, research, and community engagement.
            </p>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-xl border border-slate-200 bg-gradient-to-br from-slate-50 to-white p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5 text-[color:var(--portal-gold)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-600">Our Mission</p>
                    </div>
                    <p class="mt-2 text-sm text-slate-700">
                        Empower learners through research-driven teaching and inclusive academic support.
                    </p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-gradient-to-br from-slate-50 to-white p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5 text-[color:var(--portal-gold)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-600">Our Vision</p>
                    </div>
                    <p class="mt-2 text-sm text-slate-700">
                        To be a leading institution recognized for academic excellence and innovation.
                    </p>
                </div>
            </div>
        </div>
        <div class="space-y-6">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-[color:var(--portal-gold)] to-amber-400 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-[color:var(--portal-navy)]">Location</h2>
            </div>
            <p class="text-base text-slate-700">
                123 University Avenue, City, Country. Easily reachable via public transport with excellent connectivity.
            </p>
            <div class="overflow-hidden rounded-xl border border-slate-200 shadow-lg hover:shadow-xl transition-shadow">
                <iframe
                    class="h-64 w-full"
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3151.8354345093746!2d144.9537363153167!3d-37.81627974202137!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ad65d43f1f1f1f1%3A0xf1f1f1f1f1f1f1f1!2sUniversity!5e0!3m2!1sen!2s!4v1610000000000!5m2!1sen!2s"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                ></iframe>
            </div>
        </div>
    </section>

    {{-- Enhanced Courses Preview --}}
    <section class="space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-[color:var(--portal-navy)] mb-2">Explore Learning</p>
                <h2 class="text-3xl md:text-4xl font-bold text-[color:var(--portal-navy)]">Featured Courses</h2>
                <p class="text-base text-slate-600 mt-2">Discover our comprehensive range of programs designed for your success.</p>
            </div>
            <a href="{{ route('guest.courses') }}" class="hidden md:flex items-center gap-2 rounded-full bg-[color:var(--portal-navy)] px-6 py-3 text-sm font-semibold text-white shadow-lg hover:bg-slate-800 hover:scale-105 transition-all duration-300">
                View All
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($publicCourses as $course)
                <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-md hover:shadow-2xl transition-all duration-300 hover:-translate-y-2">
                    {{-- Gradient Header --}}
                    <div class="h-2 bg-gradient-to-r from-[color:var(--portal-navy)] via-[color:var(--portal-gold)] to-[color:var(--portal-navy)]"></div>
                    
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-[color:var(--portal-navy)] to-slate-700 flex items-center justify-center text-white font-bold text-sm">
                                    {{ substr($course->course_code, 0, 2) }}
                                </div>
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-[color:var(--portal-navy)]">{{ $course->course_code }}</p>
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $course->semester }}</p>
                                </div>
                            </div>
                            <span class="flex-shrink-0 rounded-full bg-[color:var(--portal-gold)]/20 px-3 py-1 text-xs font-bold text-[color:var(--portal-navy)]">
                                {{ $course->credits }} Cr
                            </span>
                        </div>
                        
                        <h3 class="text-xl font-bold text-slate-900 mb-3 group-hover:text-[color:var(--portal-navy)] transition-colors">
                            {{ $course->title }}
                        </h3>
                        
                        <div class="flex items-center gap-2 text-sm text-slate-600 pt-4 border-t border-slate-100">
                            <svg class="w-4 h-4 text-[color:var(--portal-gold)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            <span>Enroll now to get started</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <p class="text-base text-slate-600">No courses available at the moment.</p>
                </div>
            @endforelse
        </div>
        <div class="flex md:hidden justify-center pt-4">
            <a href="{{ route('guest.courses') }}" class="inline-flex items-center gap-2 rounded-full bg-[color:var(--portal-navy)] px-6 py-3 text-sm font-semibold text-white shadow-lg hover:bg-slate-800 transition-colors">
                View All Courses
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </section>

    {{-- Testimonials Section --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[color:var(--portal-navy)] via-slate-800 to-[color:var(--portal-navy)] p-8 md:p-12 text-white shadow-2xl">
        <div class="absolute top-0 right-0 w-64 h-64 bg-[color:var(--portal-gold)]/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl"></div>
        
        <div class="relative z-10 space-y-8">
            <div class="text-center max-w-2xl mx-auto">
                <p class="text-xs font-semibold uppercase tracking-wide text-amber-200 mb-3">What Our Community Says</p>
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Success Stories</h2>
                <p class="text-slate-300">Hear from students, alumni, and faculty about their experiences.</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-6">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20 hover:bg-white/15 transition-all">
                    <div class="flex items-center gap-1 mb-4 text-amber-300">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    </div>
                    <p class="text-slate-200 mb-4 leading-relaxed">"The courses here have transformed my career. The faculty is exceptional and the resources are world-class."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[color:var(--portal-gold)] to-amber-400 flex items-center justify-center font-bold text-white">SM</div>
                        <div>
                            <p class="font-semibold text-white">Sarah Mitchell</p>
                            <p class="text-xs text-slate-400">Computer Science Graduate</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20 hover:bg-white/15 transition-all">
                    <div class="flex items-center gap-1 mb-4 text-amber-300">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    </div>
                    <p class="text-slate-200 mb-4 leading-relaxed">"Outstanding support system and modern facilities. The online portal makes everything so accessible and organized."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center font-bold text-white">JD</div>
                        <div>
                            <p class="font-semibold text-white">James Davis</p>
                            <p class="text-xs text-slate-400">Business Administration</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20 hover:bg-white/15 transition-all">
                    <div class="flex items-center gap-1 mb-4 text-amber-300">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    </div>
                    <p class="text-slate-200 mb-4 leading-relaxed">"As a faculty member, I'm proud to be part of an institution that truly values both teaching excellence and student success."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center font-bold text-white">PW</div>
                        <div>
                            <p class="font-semibold text-white">Prof. Williams</p>
                            <p class="text-xs text-slate-400">Department Head</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Enhanced News Preview --}}
    <section class="space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-[color:var(--portal-navy)] mb-2">Stay Updated</p>
                <h2 class="text-3xl md:text-4xl font-bold text-[color:var(--portal-navy)]">Latest Announcements</h2>
                <p class="text-base text-slate-600 mt-2">Important updates, events, and news from campus.</p>
            </div>
            <a href="{{ route('guest.news') }}" class="hidden md:flex items-center gap-2 rounded-full bg-[color:var(--portal-navy)] px-6 py-3 text-sm font-semibold text-white shadow-lg hover:bg-slate-800 hover:scale-105 transition-all duration-300">
                View All
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
        <div class="grid gap-4 md:grid-cols-2">
            @forelse($publicAnnouncements as $news)
                <article class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                    <div class="absolute top-0 left-0 w-1 h-full bg-gradient-to-b from-[color:var(--portal-navy)] to-[color:var(--portal-gold)]"></div>
                    <div class="pl-4">
                        <div class="flex items-start justify-between gap-4 mb-3">
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-slate-900 group-hover:text-[color:var(--portal-navy)] transition-colors mb-2">
                                    {{ $news->title }}
                                </h3>
                                <div class="flex items-center gap-2 text-xs text-slate-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>{{ $news->created_at->format('F d, Y') }}</span>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[color:var(--portal-gold)]/20 to-[color:var(--portal-gold)]/10 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-[color:var(--portal-gold)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <p class="text-sm text-slate-700 leading-relaxed line-clamp-3">
                            {{ \Illuminate\Support\Str::limit($news->body, 140) }}
                        </p>
                    </div>
                </article>
            @empty
                <div class="col-span-full text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                    </svg>
                    <p class="text-base text-slate-600">No announcements at the moment.</p>
                </div>
            @endforelse
        </div>
        <div class="flex md:hidden justify-center pt-4">
            <a href="{{ route('guest.news') }}" class="inline-flex items-center gap-2 rounded-full bg-[color:var(--portal-navy)] px-6 py-3 text-sm font-semibold text-white shadow-lg hover:bg-slate-800 transition-colors">
                View All News
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </section>

    {{-- Enhanced Contact Section --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-50 via-white to-slate-100 border-2 border-slate-200 p-8 md:p-12 shadow-xl">
        <div class="grid gap-8 lg:grid-cols-2 lg:items-center">
            <div class="space-y-6">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-[color:var(--portal-navy)] mb-2">Get in Touch</p>
                    <h2 class="text-3xl md:text-4xl font-bold text-[color:var(--portal-navy)] mb-4">Contact Us</h2>
                    <p class="text-base text-slate-700">We're here to help. Reach out to us through any of these channels.</p>
                </div>
                
                <div class="space-y-4">
                    <div class="flex items-start gap-4 p-4 rounded-xl bg-white border border-slate-200 hover:shadow-md transition-shadow">
                        <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-[color:var(--portal-navy)] to-slate-700 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900 mb-1">Email</p>
                            <a href="mailto:info@university.edu" class="text-sm text-slate-600 hover:text-[color:var(--portal-navy)] transition-colors">info@university.edu</a>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-4 p-4 rounded-xl bg-white border border-slate-200 hover:shadow-md transition-shadow">
                        <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-[color:var(--portal-gold)] to-amber-400 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900 mb-1">Phone</p>
                            <a href="tel:+1234567890" class="text-sm text-slate-600 hover:text-[color:var(--portal-navy)] transition-colors">+123 456 7890</a>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-4 p-4 rounded-xl bg-white border border-slate-200 hover:shadow-md transition-shadow">
                        <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900 mb-1">Address</p>
                            <p class="text-sm text-slate-600">123 University Avenue, City, Country</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center gap-4 pt-4">
                    <span class="text-sm font-semibold text-slate-700">Follow us:</span>
                    <div class="flex gap-3">
                        <a href="#" class="w-10 h-10 rounded-full bg-[color:var(--portal-navy)] text-white flex items-center justify-center hover:bg-slate-800 hover:scale-110 transition-all" aria-label="Facebook">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-[color:var(--portal-navy)] text-white flex items-center justify-center hover:bg-slate-800 hover:scale-110 transition-all" aria-label="Twitter">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-[color:var(--portal-navy)] text-white flex items-center justify-center hover:bg-slate-800 hover:scale-110 transition-all" aria-label="Instagram">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="flex flex-col items-center justify-center gap-6 p-8 rounded-2xl bg-gradient-to-br from-[color:var(--portal-navy)] to-slate-800 text-white text-center shadow-2xl">
                <div class="w-20 h-20 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold mb-2">Ready to Get Started?</h3>
                <p class="text-slate-300 mb-6">Join our community of learners and start your journey today.</p>
                <div class="flex flex-col sm:flex-row gap-3 w-full">
                    <a href="{{ route('login') }}" class="flex-1 rounded-full bg-white px-6 py-3 text-base font-semibold text-[color:var(--portal-navy)] shadow-lg hover:bg-slate-100 hover:scale-105 transition-all duration-300 text-center">
                        Login
                    </a>
                    @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="flex-1 rounded-full border-2 border-white/30 bg-white/10 backdrop-blur-sm px-6 py-3 text-base font-semibold text-white hover:bg-white/20 transition-all duration-300 text-center">
                        Register
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

