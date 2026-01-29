@extends('layouts.guest')

@section('title', 'News & Announcements')

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
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.6s ease-out;
    }
    .announcement-card {
        animation: fadeInUp 0.6s ease-out;
    }
    .announcement-card:nth-child(1) { animation-delay: 0.1s; }
    .announcement-card:nth-child(2) { animation-delay: 0.2s; }
    .announcement-card:nth-child(3) { animation-delay: 0.3s; }
    .announcement-card:nth-child(4) { animation-delay: 0.4s; }
    .announcement-card:nth-child(5) { animation-delay: 0.5s; }
    .pinned-badge {
        animation: pulse 2s ease-in-out infinite;
    }
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                        </svg>
                        Stay Informed
                    </p>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-3">
                        News &
                        <span class="block text-transparent bg-clip-text bg-gradient-to-r from-[color:var(--portal-gold)] to-amber-300">
                            Announcements
                        </span>
                    </h1>
                    <p class="text-lg md:text-xl text-slate-200 max-w-2xl">
                        Stay updated with the latest campus news, events, and important announcements.
                    </p>
                    <p class="mt-3 text-sm md:text-base text-slate-200/90 max-w-2xl">
                        This page brings together official messages from the university, including academic calendar updates, examination notices, fee reminders, student opportunities, and campus events. Items marked as <strong>Urgent</strong> or <strong>Important</strong> highlight information that may require quick action from students or staff.
                    </p>
                    <p class="mt-2 text-sm md:text-base text-slate-200/90 max-w-2xl">
                        Use the search and filters to focus on pinned items, urgent notices, or information stories. Checking this page regularly helps you stay ahead of key dates, new policies, scholarship announcements, and opportunities to participate in seminars, workshops, and community projects.
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
                <div class="relative flex gap-3">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input
                            type="text"
                            id="newsSearch"
                            placeholder="Search announcements by title or content..."
                            class="w-full pl-12 pr-4 py-4 rounded-2xl bg-white/10 backdrop-blur-sm border border-white/20 text-white placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-[color:var(--portal-gold)] focus:border-transparent transition-all"
                        >
                    </div>
                    <button
                        id="newsSearchButton"
                        type="button"
                        class="inline-flex items-center gap-2 rounded-2xl bg-white/90 px-4 md:px-5 py-3 text-sm font-semibold text-[color:var(--portal-navy)] shadow-lg hover:bg-white hover:scale-105 transition-all"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.2-5.2M11 18a7 7 0 100-14 7 7 0 000 14z"/>
                        </svg>
                        <span>Search</span>
                    </button>
                </div>
            </div>
        </div>
    </section>

    {{-- News Highlights Slider --}}
    <section class="space-y-4">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-[color:var(--portal-navy)] mb-1">Latest Highlights</p>
                <h2 class="text-2xl md:text-3xl font-bold text-[color:var(--portal-navy)]">Campus News & Events</h2>
                <p class="text-sm md:text-base text-slate-600">
                    Visual highlights from important announcements, events, research stories, and campus life.
                </p>
            </div>
        </div>

        <div class="portal-slider rounded-3xl border border-slate-200 bg-slate-900/90 shadow-xl overflow-hidden" data-portal-slider data-autoplay="true" data-interval="6000">
            <div class="portal-slider-track relative">
                <div class="portal-slide is-active" data-portal-slide>
                    <div class="absolute inset-0 overflow-hidden">
                        <div class="portal-slide-image" style="background-image: url('{{ asset('images/news/announce.png') }}'); background-size: cover; background-position: center;"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/40 to-transparent"></div>
                        <div class="absolute inset-0 flex items-end">
                            <div class="p-6 md:p-8 space-y-1 text-white">
                                <h3 class="text-lg md:text-xl font-bold">University Announcements</h3>
                                <p class="text-sm md:text-base text-slate-100/90 max-w-xl">Official updates, deadlines, and policy changes that matter to students and staff.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="portal-slide" data-portal-slide>
                    <div class="absolute inset-0 overflow-hidden">
                        <div class="portal-slide-image" style="background-image: url('{{ asset('images/news/event.jpg') }}'); background-size: cover; background-position: center;"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/40 to-transparent"></div>
                        <div class="absolute inset-0 flex items-end">
                            <div class="p-6 md:p-8 space-y-1 text-white">
                                <h3 class="text-lg md:text-xl font-bold">Events & Activities</h3>
                                <p class="text-sm md:text-base text-slate-100/90 max-w-xl">Seminars, workshops, and campus events that enrich learning and bring the community together.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="portal-slide" data-portal-slide>
                    <div class="absolute inset-0 overflow-hidden">
                        <div class="portal-slide-image" style="background-image: url('{{ asset('images/news/lab.jpg') }}'); background-size: cover; background-position: center;"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/40 to-transparent"></div>
                        <div class="absolute inset-0 flex items-end">
                            <div class="p-6 md:p-8 space-y-1 text-white">
                                <h3 class="text-lg md:text-xl font-bold">Research & Innovation</h3>
                                <p class="text-sm md:text-base text-slate-100/90 max-w-xl">Research breakthroughs, grants, and faculty achievements that drive innovation and impact.</p>
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
    @include('guest.partials.image-cards-news')

    {{-- Statistics Overview --}}
    <section class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="rounded-2xl border border-slate-200 bg-gradient-to-br from-white to-slate-50 p-6 shadow-md hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[color:var(--portal-navy)] to-slate-700 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl md:text-3xl font-bold text-[color:var(--portal-navy)]">{{ $announcements->count() }}</div>
                    <div class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Total News</div>
                </div>
            </div>
        </div>
        
        <div class="rounded-2xl border border-slate-200 bg-gradient-to-br from-white to-slate-50 p-6 shadow-md hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl md:text-3xl font-bold text-[color:var(--portal-navy)]">{{ $announcements->where('priority', 'urgent')->count() }}</div>
                    <div class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Urgent</div>
                </div>
            </div>
        </div>
        
        <div class="rounded-2xl border border-slate-200 bg-gradient-to-br from-white to-slate-50 p-6 shadow-md hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl md:text-3xl font-bold text-[color:var(--portal-navy)]">{{ $announcements->where('priority', 'important')->count() }}</div>
                    <div class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Important</div>
                </div>
            </div>
        </div>
        
        <div class="rounded-2xl border border-slate-200 bg-gradient-to-br from-white to-slate-50 p-6 shadow-md hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[color:var(--portal-gold)] to-amber-400 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl md:text-3xl font-bold text-[color:var(--portal-navy)]">{{ $announcements->where('pinned', true)->count() }}</div>
                    <div class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Pinned</div>
                </div>
            </div>
        </div>
    </section>

    {{-- Visual Feature Cards --}}
    <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-gradient-to-br from-red-50 to-white p-6 shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="absolute top-0 right-0 w-32 h-32 bg-red-200/20 rounded-full blur-2xl"></div>
            <div class="relative z-10">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-xl bg-gradient-to-br from-red-500 to-red-600 text-white mb-4 shadow-lg group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Urgent Updates</h3>
                <p class="text-sm text-slate-600">Stay informed about critical announcements and deadlines.</p>
            </div>
        </div>
        
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-gradient-to-br from-purple-50 to-white p-6 shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="absolute top-0 right-0 w-32 h-32 bg-purple-200/20 rounded-full blur-2xl"></div>
            <div class="relative z-10">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 text-white mb-4 shadow-lg group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Campus Events</h3>
                <p class="text-sm text-slate-600">Discover workshops, seminars, and social activities.</p>
            </div>
        </div>
        
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-gradient-to-br from-indigo-50 to-white p-6 shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-200/20 rounded-full blur-2xl"></div>
            <div class="relative z-10">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 text-white mb-4 shadow-lg group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Research News</h3>
                <p class="text-sm text-slate-600">Latest breakthroughs and academic achievements.</p>
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
        
        <button data-filter="all" class="filter-btn active px-4 py-2 rounded-lg border border-slate-300 bg-[color:var(--portal-navy)] text-white text-sm font-medium hover:bg-slate-800 transition-colors">
            All
        </button>
        
        <button data-filter="pinned" class="filter-btn px-4 py-2 rounded-lg border border-slate-300 bg-white text-slate-700 text-sm font-medium hover:bg-slate-50 transition-colors">
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                </svg>
                Pinned
            </span>
        </button>
        
        <button data-filter="urgent" class="filter-btn px-4 py-2 rounded-lg border border-slate-300 bg-white text-slate-700 text-sm font-medium hover:bg-slate-50 transition-colors">
            <span class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                Urgent
            </span>
        </button>
        
        <button data-filter="important" class="filter-btn px-4 py-2 rounded-lg border border-slate-300 bg-white text-slate-700 text-sm font-medium hover:bg-slate-50 transition-colors">
            <span class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                Important
            </span>
        </button>
        
        <button data-filter="info" class="filter-btn px-4 py-2 rounded-lg border border-slate-300 bg-white text-slate-700 text-sm font-medium hover:bg-slate-50 transition-colors">
            <span class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                Info
            </span>
        </button>
        
        <button id="clearFilters" class="ml-auto px-4 py-2 rounded-lg border border-slate-300 bg-white text-slate-700 text-sm font-medium hover:bg-slate-50 transition-colors">
            Clear Filters
        </button>
    </section>

    {{-- Announcements List --}}
    <section>
        <div id="announcementsContainer" class="space-y-4">
            @forelse($announcements as $item)
                @php
                    $priority = $item->priority ?? 'info';
                    $isPinned = $item->pinned ?? false;
                    $priorityColors = [
                        'urgent' => ['bg' => 'bg-red-50', 'border' => 'border-red-200', 'badge' => 'bg-red-500', 'text' => 'text-red-700', 'icon' => 'text-red-600'],
                        'important' => ['bg' => 'bg-amber-50', 'border' => 'border-amber-200', 'badge' => 'bg-amber-500', 'text' => 'text-amber-700', 'icon' => 'text-amber-600'],
                        'info' => ['bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'badge' => 'bg-blue-500', 'text' => 'text-blue-700', 'icon' => 'text-blue-600'],
                    ];
                    $colors = $priorityColors[$priority] ?? $priorityColors['info'];
                @endphp
                
                <article class="announcement-card group relative overflow-hidden rounded-2xl border-2 {{ $isPinned ? 'border-[color:var(--portal-gold)] bg-gradient-to-br from-amber-50 to-white' : 'border-' . $colors['border'] . ' bg-white' }} shadow-md hover:shadow-2xl transition-all duration-300 hover:-translate-y-1" 
                         data-priority="{{ $priority }}" 
                         data-pinned="{{ $isPinned ? 'true' : 'false' }}"
                         data-title="{{ htmlspecialchars(strtolower(strip_tags($item->title)), ENT_QUOTES, 'UTF-8') }}"
                         data-body="{{ htmlspecialchars(strtolower(strip_tags($item->body)), ENT_QUOTES, 'UTF-8') }}">
                    
                    {{-- Priority Indicator Bar --}}
                    <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r {{ $priority === 'urgent' ? 'from-red-500 to-red-600' : ($priority === 'important' ? 'from-amber-500 to-amber-600' : 'from-blue-500 to-blue-600') }}"></div>
                    
                    <div class="p-6">
                        <div class="flex items-start justify-between gap-4 mb-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-3 flex-wrap">
                                    @if($isPinned)
                                        <span class="pinned-badge inline-flex items-center gap-1 rounded-full bg-[color:var(--portal-gold)] px-3 py-1 text-xs font-bold text-[color:var(--portal-navy)] shadow-md">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                            </svg>
                                            Pinned
                                        </span>
                                    @endif
                                    
                                    <span class="inline-flex items-center gap-1 rounded-full {{ $colors['badge'] }} px-3 py-1 text-xs font-bold text-white shadow-md">
                                        @if($priority === 'urgent')
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                        @elseif($priority === 'important')
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                        @else
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                        {{ ucfirst($priority) }}
                                    </span>
                                </div>
                                
                                <h2 class="text-2xl font-bold text-slate-900 mb-3 group-hover:text-[color:var(--portal-navy)] transition-colors">
                                    {{ $item->title }}
                                </h2>
                            </div>
                            
                            <div class="flex-shrink-0 text-right">
                                <div class="flex items-center gap-2 text-sm text-slate-500 mb-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>{{ $item->created_at->format('M d, Y') }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>{{ $item->created_at->format('h:i A') }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="prose prose-sm max-w-none">
                            <p class="text-base text-slate-700 leading-relaxed whitespace-pre-wrap">{{ $item->body }}</p>
                        </div>
                    </div>
                </article>
            @empty
                <div class="text-center py-16 px-4">
                    <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-gradient-to-br from-slate-100 to-slate-200 mb-6">
                        <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-2">No Announcements Available</h3>
                    <p class="text-slate-600 mb-6">Check back soon for the latest news and updates.</p>
                    <a href="{{ route('guest.home') }}" class="inline-flex items-center gap-2 rounded-full bg-[color:var(--portal-navy)] px-6 py-3 text-sm font-semibold text-white shadow-lg hover:bg-slate-800 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Home
                    </a>
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
            <h3 class="text-2xl font-bold text-slate-900 mb-2">No Announcements Found</h3>
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
    
    function initNewsFilters() {
        const announcementCards = Array.from(document.querySelectorAll('.announcement-card'));
        const searchInput = document.getElementById('newsSearch');
        const searchButton = document.getElementById('newsSearchButton');
        const filterButtons = document.querySelectorAll('.filter-btn');
        const clearFiltersBtn = document.getElementById('clearFilters');
        const clearSearchFiltersBtn = document.getElementById('clearSearchFilters');
        const announcementsContainer = document.getElementById('announcementsContainer');
        const noResults = document.getElementById('noResults');
        
        // Check if required elements exist
        if (!searchInput || !announcementsContainer || announcementCards.length === 0) {
            console.warn('News filter elements not found or no announcements available');
            return;
        }
        
        let currentFilter = 'all';
        
        function filterAnnouncements(shouldScroll = false) {
            const searchTerm = (searchInput.value || '').trim().toLowerCase();
            let visibleCount = 0;
            
            announcementCards.forEach(card => {
                const title = (card.dataset.title || '').trim();
                const body = (card.dataset.body || '').trim();
                const priority = (card.dataset.priority || 'info').trim();
                const isPinned = card.dataset.pinned === 'true';
                
                // Search filter
                const matchesSearch = !searchTerm || 
                    title.includes(searchTerm) || 
                    body.includes(searchTerm);
                
                // Priority/Pinned filter
                let matchesFilter = false;
                switch(currentFilter) {
                    case 'pinned':
                        matchesFilter = isPinned;
                        break;
                    case 'urgent':
                        matchesFilter = priority === 'urgent';
                        break;
                    case 'important':
                        matchesFilter = priority === 'important';
                        break;
                    case 'info':
                        matchesFilter = priority === 'info';
                        break;
                    case 'all':
                    default:
                        matchesFilter = true;
                        break;
                }
                
                if (matchesSearch && matchesFilter) {
                    card.style.display = 'block';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
            
            // Show/hide no results message
            if (visibleCount === 0) {
                announcementsContainer.style.display = 'none';
                if (noResults) {
                    noResults.classList.remove('hidden');
                    if (shouldScroll && typeof noResults.scrollIntoView === 'function') {
                        noResults.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            } else {
                announcementsContainer.style.display = 'block';
                if (noResults) noResults.classList.add('hidden');
                if (shouldScroll && searchTerm && typeof announcementsContainer.scrollIntoView === 'function') {
                    announcementsContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        }
        
        function setActiveFilter(filter) {
            currentFilter = filter;
            filterButtons.forEach(btn => {
                if (btn.dataset.filter === filter) {
                    btn.classList.add('active', 'bg-[color:var(--portal-navy)]', 'text-white');
                    btn.classList.remove('bg-white', 'text-slate-700');
                } else {
                    btn.classList.remove('active', 'bg-[color:var(--portal-navy)]', 'text-white');
                    btn.classList.add('bg-white', 'text-slate-700');
                }
            });
        }
        
        function clearAllFilters() {
            searchInput.value = '';
            setActiveFilter('all');
            filterAnnouncements();
        }
        
        // Event listeners
        // Live filtering while typing, but without auto-scroll
        searchInput.addEventListener('input', () => filterAnnouncements(false));
        searchInput.addEventListener('keyup', (event) => {
            if (event.key === 'Enter') {
                filterAnnouncements(true);
            } else {
                filterAnnouncements(false);
            }
        });
        
        filterButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                setActiveFilter(this.dataset.filter);
                filterAnnouncements(false);
            });
        });
        
        if (clearFiltersBtn) {
            clearFiltersBtn.addEventListener('click', clearAllFilters);
        }
        if (clearSearchFiltersBtn) {
            clearSearchFiltersBtn.addEventListener('click', clearAllFilters);
        }

        if (searchButton) {
            searchButton.addEventListener('click', () => filterAnnouncements(true));
        }
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initNewsFilters);
    } else {
        initNewsFilters();
    }
})();
</script>
@endpush
@endsection

