@extends('layouts.guest')

@section('title', 'Courses')

@push('styles')
<style>
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.6s ease-out;
    }
    .course-card {
        animation: fadeInUp 0.6s ease-out;
    }
    .course-card:nth-child(1) { animation-delay: 0.1s; }
    .course-card:nth-child(2) { animation-delay: 0.2s; }
    .course-card:nth-child(3) { animation-delay: 0.3s; }
    .course-card:nth-child(4) { animation-delay: 0.4s; }
    .course-card:nth-child(5) { animation-delay: 0.5s; }
    .course-card:nth-child(6) { animation-delay: 0.6s; }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6 space-y-8">
    {{-- Hero Section with Search --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[color:var(--portal-navy)] via-slate-800 to-[color:var(--portal-navy)] px-6 md:px-12 py-12 md:py-16 text-white shadow-2xl">
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-0 right-0 w-96 h-96 bg-[color:var(--portal-gold)] rounded-full mix-blend-multiply filter blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl"></div>
        </div>
        
        <div class="relative z-10 space-y-6">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <p class="inline-flex items-center gap-2 rounded-full bg-white/10 backdrop-blur-sm px-4 py-2 text-xs font-semibold uppercase tracking-wide text-amber-200 ring-1 ring-white/20 mb-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        Explore Our Programs
                    </p>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-3">
                        Discover Your
                        <span class="block text-transparent bg-clip-text bg-gradient-to-r from-[color:var(--portal-gold)] to-amber-300">
                            Perfect Course
                        </span>
                    </h1>
                    <p class="text-lg md:text-xl text-slate-200 max-w-2xl">
                        Browse through our comprehensive catalog of courses designed to shape your future.
                    </p>
                </div>
                <a href="{{ route('guest.home') }}" class="flex items-center gap-2 rounded-full border border-white/30 backdrop-blur-sm bg-white/10 px-6 py-3 text-sm font-semibold text-white hover:bg-white/20 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Home
                </a>
            </div>
            
            {{-- Search Bar --}}
            <div class="max-w-2xl">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input
                        type="text"
                        id="courseSearch"
                        placeholder="Search courses by name, code, or semester..."
                        class="w-full pl-12 pr-4 py-4 rounded-2xl bg-white/10 backdrop-blur-sm border border-white/20 text-white placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-[color:var(--portal-gold)] focus:border-transparent transition-all"
                    >
                </div>
            </div>
        </div>
    </section>

    {{-- Courses Showcase Slider --}}
    <section class="space-y-4">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-[color:var(--portal-navy)] mb-1">Course Highlights</p>
                <h2 class="text-2xl md:text-3xl font-bold text-[color:var(--portal-navy)]">Explore Programs</h2>
                <p class="text-sm md:text-base text-slate-600">
                    Add your own course banners or faculty photos here to introduce key programs.
                </p>
            </div>
        </div>

        <div class="portal-slider rounded-3xl border border-slate-200 bg-slate-900/90 shadow-xl overflow-hidden" data-portal-slider data-autoplay="true" data-interval="7000">
            <div class="portal-slider-track relative h-48 md:h-64 lg:h-72">
                <div class="portal-slide is-active" data-portal-slide>
                    <div class="relative h-full w-full">
                        <div class="h-full w-full bg-cover bg-center" style="background-image: url('{{ asset('images/courses/slide-1.jpg') }}');"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/40 to-transparent"></div>
                        <div class="absolute inset-0 flex items-end">
                            <div class="p-6 md:p-8 space-y-1 text-white">
                                <h3 class="text-lg md:text-xl font-bold">Featured Programs</h3>
                                <p class="text-sm md:text-base text-slate-100/90 max-w-xl">Showcase your most popular or flagship courses.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="portal-slide" data-portal-slide>
                    <div class="relative h-full w-full">
                        <div class="h-full w-full bg-cover bg-center" style="background-image: url('{{ asset('images/courses/slide-2.jpg') }}');"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/40 to-transparent"></div>
                        <div class="absolute inset-0 flex items-end">
                            <div class="p-6 md:p-8 space-y-1 text-white">
                                <h3 class="text-lg md:text-xl font-bold">Faculty & Mentors</h3>
                                <p class="text-sm md:text-base text-slate-100/90 max-w-xl">Highlight teaching staff or guest lecturers.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="portal-slide" data-portal-slide>
                    <div class="relative h-full w-full">
                        <div class="h-full w-full bg-cover bg-center" style="background-image: url('{{ asset('images/courses/slide-3.jpg') }}');"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/40 to-transparent"></div>
                        <div class="absolute inset-0 flex items-end">
                            <div class="p-6 md:p-8 space-y-1 text-white">
                                <h3 class="text-lg md:text-xl font-bold">Labs & Learning Spaces</h3>
                                <p class="text-sm md:text-base text-slate-100/90 max-w-xl">Use this to show labs, studios, or fieldwork photos.</p>
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

    {{-- Statistics Overview --}}
    <section class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="rounded-2xl border border-slate-200 bg-gradient-to-br from-white to-slate-50 p-6 shadow-md hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[color:var(--portal-navy)] to-slate-700 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl md:text-3xl font-bold text-[color:var(--portal-navy)]">{{ number_format($stats['totalCourses']) }}</div>
                    <div class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Total Courses</div>
                </div>
            </div>
        </div>
        
        <div class="rounded-2xl border border-slate-200 bg-gradient-to-br from-white to-slate-50 p-6 shadow-md hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[color:var(--portal-gold)] to-amber-400 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl md:text-3xl font-bold text-[color:var(--portal-navy)]">{{ number_format($stats['uniqueSemesters']) }}</div>
                    <div class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Semesters</div>
                </div>
            </div>
        </div>
        
        <div class="rounded-2xl border border-slate-200 bg-gradient-to-br from-white to-slate-50 p-6 shadow-md hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl md:text-3xl font-bold text-[color:var(--portal-navy)]">{{ number_format($stats['totalCredits']) }}</div>
                    <div class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Total Credits</div>
                </div>
            </div>
        </div>
        
        <div class="rounded-2xl border border-slate-200 bg-gradient-to-br from-white to-slate-50 p-6 shadow-md hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl md:text-3xl font-bold text-[color:var(--portal-navy)]">{{ $stats['availabilityRate'] }}%</div>
                    <div class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Available</div>
                </div>
            </div>
        </div>
    </section>

    {{-- Filters Section --}}
    <section class="flex flex-wrap items-center gap-4 p-4 rounded-2xl bg-white border border-slate-200 shadow-sm">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
            </svg>
            <span class="text-sm font-semibold text-slate-700">Filter:</span>
        </div>
        
        <select id="semesterFilter" class="px-4 py-2 rounded-lg border border-slate-300 bg-white text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-[color:var(--portal-navy)] focus:border-transparent cursor-pointer">
            <option value="">All Semesters</option>
            @foreach($courses->pluck('semester')->unique()->sort() as $semester)
                <option value="{{ $semester }}">{{ $semester }}</option>
            @endforeach
        </select>
        
        <select id="sortFilter" class="px-4 py-2 rounded-lg border border-slate-300 bg-white text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-[color:var(--portal-navy)] focus:border-transparent cursor-pointer">
            <option value="code">Sort by Code</option>
            <option value="title">Sort by Title</option>
            <option value="credits">Sort by Credits</option>
            <option value="semester">Sort by Semester</option>
        </select>
        
        <button id="clearFilters" class="ml-auto px-4 py-2 rounded-lg border border-slate-300 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
            Clear Filters
        </button>
    </section>

    {{-- Courses Grid --}}
    <section>
        <div id="coursesContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($courses as $course)
                <div class="course-card group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-md hover:shadow-2xl transition-all duration-300 hover:-translate-y-2"
                     data-course-code="{{ strtolower($course->course_code) }}"
                     data-course-title="{{ strtolower($course->title) }}"
                     data-semester="{{ strtolower($course->semester ?? '') }}"
                     data-credits="{{ $course->credits }}">
                    {{-- Gradient Header Bar --}}
                    <div class="h-2 bg-gradient-to-r from-[color:var(--portal-navy)] via-[color:var(--portal-gold)] to-[color:var(--portal-navy)]"></div>
                    
                    {{-- Course Image/Icon --}}
                    <div class="relative h-48 overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200">
                        @if ($course->photo)
                            <img
                                src="{{ asset('storage/'.$course->photo) }}"
                                alt="Course photo for {{ $course->title }}"
                                class="h-full w-full object-cover group-hover:scale-110 transition-transform duration-500"
                            >
                        @else
                            <div class="h-full w-full flex items-center justify-center">
                                <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-[color:var(--portal-navy)] to-slate-700 flex items-center justify-center text-white text-4xl font-bold shadow-lg">
                                    {{ Str::of($course->title)->substr(0, 1) }}
                                </div>
                            </div>
                        @endif
                        <div class="absolute top-4 right-4">
                            <span class="inline-flex items-center gap-1 rounded-full bg-white/90 backdrop-blur-sm px-3 py-1.5 text-xs font-bold text-[color:var(--portal-navy)] shadow-lg">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                {{ $course->credits }} Cr
                            </span>
                        </div>
                    </div>
                    
                    {{-- Course Content --}}
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-[color:var(--portal-navy)] to-slate-700 flex items-center justify-center text-white text-xs font-bold">
                                    {{ substr($course->course_code, 0, 2) }}
                                </div>
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wide text-[color:var(--portal-navy)] course-code">{{ $course->course_code }}</p>
                                    <p class="text-xs text-slate-500 course-semester">{{ $course->semester }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <h3 class="text-xl font-bold text-slate-900 mb-3 group-hover:text-[color:var(--portal-navy)] transition-colors line-clamp-2 course-title">
                            {{ $course->title }}
                        </h3>
                        
                        <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                            <div class="flex items-center gap-2 text-sm text-slate-600">
                                <svg class="w-4 h-4 text-[color:var(--portal-gold)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>{{ $course->semester }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm font-semibold text-[color:var(--portal-navy)]">
                                <span>View Details</span>
                                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="text-center py-16 px-4">
                        <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-gradient-to-br from-slate-100 to-slate-200 mb-6">
                            <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900 mb-2">No Courses Available</h3>
                        <p class="text-slate-600 mb-6">Check back soon for new course offerings.</p>
                        <a href="{{ route('guest.home') }}" class="inline-flex items-center gap-2 rounded-full bg-[color:var(--portal-navy)] px-6 py-3 text-sm font-semibold text-white shadow-lg hover:bg-slate-800 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Back to Home
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
        
        {{-- No Results Message (hidden by default) --}}
        <div id="noResults" class="hidden text-center py-16 px-4">
            <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-gradient-to-br from-slate-100 to-slate-200 mb-6">
                <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-slate-900 mb-2">No Courses Found</h3>
            <p class="text-slate-600 mb-6">Try adjusting your search or filters.</p>
            <button id="clearSearchFilters" class="inline-flex items-center gap-2 rounded-full bg-[color:var(--portal-navy)] px-6 py-3 text-sm font-semibold text-white shadow-lg hover:bg-slate-800 transition-colors">
                Clear All Filters
            </button>
        </div>
    </section>
</div>

@push('scripts')
<script>
(function() {
    'use strict';
    
    function initCourseFilters() {
        const courseCards = Array.from(document.querySelectorAll('.course-card'));
        const searchInput = document.getElementById('courseSearch');
        const semesterFilter = document.getElementById('semesterFilter');
        const sortFilter = document.getElementById('sortFilter');
        const clearFiltersBtn = document.getElementById('clearFilters');
        const clearSearchFiltersBtn = document.getElementById('clearSearchFilters');
        const coursesContainer = document.getElementById('coursesContainer');
        const noResults = document.getElementById('noResults');
        
        // Check if elements exist
        if (!searchInput || !semesterFilter || !sortFilter || !coursesContainer || courseCards.length === 0) {
            console.warn('Course filter elements not found or no courses available');
            return;
        }
        
        // Build course data array using data attributes
        const allCourses = courseCards.map(card => ({
            element: card,
            title: (card.dataset.courseTitle || '').trim(),
            code: (card.dataset.courseCode || '').trim(),
            semester: (card.dataset.semester || '').trim(),
            credits: parseInt(card.dataset.credits || '0', 10)
        }));
        
        function filterAndSortCourses() {
            const searchTerm = (searchInput.value || '').trim().toLowerCase();
            const selectedSemester = (semesterFilter.value || '').trim().toLowerCase();
            const sortBy = sortFilter.value || 'code';
            
            // Filter courses
            let filtered = allCourses.filter(course => {
                const matchesSearch = !searchTerm || 
                    course.title.includes(searchTerm) || 
                    course.code.includes(searchTerm) ||
                    course.semester.includes(searchTerm);
                
                const matchesSemester = !selectedSemester || course.semester === selectedSemester;
                
                return matchesSearch && matchesSemester;
            });
            
            // Sort courses
            filtered.sort((a, b) => {
                switch(sortBy) {
                    case 'title':
                        return a.title.localeCompare(b.title);
                    case 'credits':
                        return b.credits - a.credits;
                    case 'semester':
                        return a.semester.localeCompare(b.semester);
                    case 'code':
                    default:
                        return a.code.localeCompare(b.code);
                }
            });
            
            // Hide all courses
            courseCards.forEach(card => {
                card.style.display = 'none';
            });
            
            // Show filtered courses
            if (filtered.length === 0) {
                coursesContainer.style.display = 'none';
                if (noResults) noResults.classList.remove('hidden');
            } else {
                coursesContainer.style.display = 'grid';
                if (noResults) noResults.classList.add('hidden');
                
                filtered.forEach((course, index) => {
                    course.element.style.display = 'block';
                    course.element.style.order = index;
                });
            }
        }
        
        function clearAllFilters() {
            searchInput.value = '';
            semesterFilter.value = '';
            sortFilter.value = 'code';
            filterAndSortCourses();
        }
        
        // Event listeners
        searchInput.addEventListener('input', filterAndSortCourses);
        searchInput.addEventListener('keyup', filterAndSortCourses);
        semesterFilter.addEventListener('change', filterAndSortCourses);
        sortFilter.addEventListener('change', filterAndSortCourses);
        
        if (clearFiltersBtn) {
            clearFiltersBtn.addEventListener('click', clearAllFilters);
        }
        if (clearSearchFiltersBtn) {
            clearSearchFiltersBtn.addEventListener('click', clearAllFilters);
        }
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCourseFilters);
    } else {
        initCourseFilters();
    }
})();
</script>
@endpush
@endsection

