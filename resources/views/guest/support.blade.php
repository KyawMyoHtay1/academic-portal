@extends('layouts.guest')

@section('title', 'Support & Help Desk')

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
    .animate-fade-in-up {
        animation: fadeInUp 0.8s ease-out;
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                We're Here to Help
            </p>
            <h1 class="text-5xl md:text-6xl lg:text-7xl font-bold leading-tight">
                Support &
                <span class="block text-transparent bg-clip-text bg-gradient-to-r from-[color:var(--portal-gold)] to-amber-300 mt-2">
                    Help Desk
                </span>
            </h1>
        </div>
    </section>

    {{-- Support Highlights Slider --}}
    <section class="space-y-4">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-[color:var(--portal-navy)] mb-1">Help Desk Highlights</p>
                <h2 class="text-2xl md:text-3xl font-bold text-[color:var(--portal-navy)]">Support in One Place</h2>
                <p class="text-sm md:text-base text-slate-600">Add visuals for FAQs, guides, or support workflows here.</p>
            </div>
        </div>

        <div class="portal-slider rounded-3xl border border-slate-200 bg-slate-900/90 shadow-xl overflow-hidden" data-portal-slider data-autoplay="true" data-interval="6000">
            <div class="portal-slider-track relative">
                <div class="portal-slide is-active" data-portal-slide>
                    <div class="absolute inset-0 overflow-hidden">
                        <div class="portal-slide-image" style="background-image: url('{{ asset('images/home/lab.png') }}');"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/40 to-transparent"></div>
                        <div class="absolute inset-0 flex items-end">
                            <div class="p-6 md:p-8 space-y-1 text-white">
                                <h3 class="text-lg md:text-xl font-bold">FAQs & Guides</h3>
                                <p class="text-sm md:text-base text-slate-100/90 max-w-xl">Showcase self-help resources that answer common questions.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="portal-slide" data-portal-slide>
                    <div class="absolute inset-0 overflow-hidden">
                        <div class="portal-slide-image" style="background-image: url('{{ asset('images/home/lab.png') }}');"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/40 to-transparent"></div>
                        <div class="absolute inset-0 flex items-end">
                            <div class="p-6 md:p-8 space-y-1 text-white">
                                <h3 class="text-lg md:text-xl font-bold">Issue Reporting</h3>
                                <p class="text-sm md:text-base text-slate-100/90 max-w-xl">Highlight how users report issues and track resolution.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="portal-slide" data-portal-slide>
                    <div class="absolute inset-0 overflow-hidden">
                        <div class="portal-slide-image" style="background-image: url('{{ asset('images/home/lab.png') }}');"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/40 to-transparent"></div>
                        <div class="absolute inset-0 flex items-end">
                            <div class="p-6 md:p-8 space-y-1 text-white">
                                <h3 class="text-lg md:text-xl font-bold">Contact Channels</h3>
                                <p class="text-sm md:text-base text-slate-100/90 max-w-xl">Display support contacts, office hours, or live help options.</p>
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

    {{-- Explore strip --}}
    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex flex-wrap items-center justify-center gap-3 md:gap-6">
            <a href="{{ route('guest.home') }}" class="flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:border-[color:var(--portal-navy)] hover:bg-[color:var(--portal-navy)]/5 hover:text-[color:var(--portal-navy)] transition-all">Home</a>
            <a href="{{ route('guest.courses') }}" class="flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:border-[color:var(--portal-navy)] hover:bg-[color:var(--portal-navy)]/5 hover:text-[color:var(--portal-navy)] transition-all">Courses</a>
            <a href="{{ route('guest.news') }}" class="flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:border-[color:var(--portal-navy)] hover:bg-[color:var(--portal-navy)]/5 hover:text-[color:var(--portal-navy)] transition-all">News</a>
            <a href="{{ route('guest.about') }}" class="flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:border-[color:var(--portal-navy)] hover:bg-[color:var(--portal-navy)]/5 hover:text-[color:var(--portal-navy)] transition-all">About</a>
            <a href="{{ route('guest.services') }}" class="flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:border-[color:var(--portal-navy)] hover:bg-[color:var(--portal-navy)]/5 hover:text-[color:var(--portal-navy)] transition-all">Services</a>
            <a href="{{ route('guest.contact') }}" class="flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:border-[color:var(--portal-navy)] hover:bg-[color:var(--portal-navy)]/5 hover:text-[color:var(--portal-navy)] transition-all">Contact</a>
        </div>
    </section>

    {{-- Main Content --}}
    <section class="max-w-4xl mx-auto space-y-8">
        <div class="group relative overflow-hidden rounded-3xl border-2 border-slate-200 bg-gradient-to-br from-white to-slate-50 p-8 md:p-12 shadow-xl hover:shadow-2xl transition-all duration-300">
            <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-[color:var(--portal-navy)]/5 to-transparent rounded-full blur-3xl"></div>
            <div class="relative z-10 space-y-6">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-gradient-to-br from-[color:var(--portal-navy)] to-slate-700 text-white shadow-lg">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                
                <div class="prose prose-lg max-w-none">
                    <h2 class="text-3xl md:text-4xl font-bold text-[color:var(--portal-navy)] mb-6">Support & Help Desk</h2>
                    <p class="text-lg text-slate-700 leading-relaxed mb-6">
                        The Support & Help Desk section assists users in resolving technical issues and academic portal inquiries. Users can find guidance on using portal features, report system issues, and contact administrative support for assistance.
                    </p>
                    <p class="text-lg text-slate-700 leading-relaxed">
                        This ensures smooth and uninterrupted use of the academic portal.
                    </p>
                </div>
            </div>
        </div>

        {{-- Support Options --}}
        <div class="grid gap-6 md:grid-cols-2">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2">FAQs</h3>
                        <p class="text-slate-600 mb-2">Find answers to commonly asked questions.</p>
                        <div class="text-lg font-bold text-[color:var(--portal-navy)] mb-3">
                            {{ $stats['totalAnnouncements'] > 0 ? number_format($stats['totalAnnouncements']) : '0' }} Announcements
                        </div>
                        <a href="{{ route('guest.contact') }}" class="text-sm font-semibold text-[color:var(--portal-navy)] hover:text-blue-600 transition-colors">
                            Browse FAQs →
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2">User Guides</h3>
                        <p class="text-slate-600 mb-2">Step-by-step guides for portal features.</p>
                        <div class="text-lg font-bold text-[color:var(--portal-navy)] mb-3">
                            {{ $stats['totalUsers'] > 0 ? number_format($stats['totalUsers']) : '0' }} Active Users
                        </div>
                        <a href="{{ route('guest.contact') }}" class="text-sm font-semibold text-[color:var(--portal-navy)] hover:text-green-600 transition-colors">
                            View Guides →
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Report Issues</h3>
                        <p class="text-slate-600 mb-2">Report technical problems or bugs.</p>
                        <div class="text-lg font-bold text-[color:var(--portal-navy)] mb-3">
                            {{ $stats['totalFaculty'] > 0 ? number_format($stats['totalFaculty']) : '0' }} Support Staff
                        </div>
                        <a href="{{ route('guest.contact') }}" class="text-sm font-semibold text-[color:var(--portal-navy)] hover:text-purple-600 transition-colors">
                            Report Issue →
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Contact Support</h3>
                        <p class="text-slate-600 mb-2">Get direct assistance from our team.</p>
                        <div class="text-lg font-bold text-[color:var(--portal-navy)] mb-3">
                            {{ $stats['totalStudents'] > 0 ? number_format($stats['totalStudents']) : '0' }} Students Supported
                        </div>
                        <a href="{{ route('guest.contact') }}" class="text-sm font-semibold text-[color:var(--portal-navy)] hover:text-amber-600 transition-colors">
                            Contact Us →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
