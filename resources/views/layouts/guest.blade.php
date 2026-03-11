<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'University Academic Portal')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @if (config('recaptcha.site_key'))
        <!-- Google reCAPTCHA v3 -->
        <script src="https://www.google.com/recaptcha/api.js?render={{ config('recaptcha.site_key') }}"></script>
    @endif

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Merriweather:wght@700&family=Source+Sans+3:wght@400;500;600;700&display=swap');

        :root {
            --portal-navy: #0b1f3a;
            --portal-gold: #f4b400;
            --portal-ink: #081326;
            --portal-teal: #1f7a8c;
            --portal-cream: #f8fafc;
            --portal-stone: #8b7c5a;
        }
        /* Hide Google Translate's default UI */
        .goog-te-banner-frame,
        .goog-te-menu-value,
        .goog-te-menu-frame {
            display: none !important;
        }
        body {
            top: 0 !important;
            font-family: "Source Sans 3", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background:
                radial-gradient(66rem 66rem at -18% -18%, rgba(56, 93, 128, 0.18), transparent 58%),
                radial-gradient(52rem 52rem at 115% -12%, rgba(141, 124, 92, 0.14), transparent 52%),
                linear-gradient(180deg, #f7f9fc 0%, #f4f7fb 45%, #f8fafd 100%);
            color: #0f172a;
        }
        h1, h2, h3, h4 {
            font-family: "Merriweather", "Source Sans 3", serif;
            letter-spacing: 0;
        }
        .guest-shell-bg {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }
        .guest-orb {
            position: absolute;
            border-radius: 999px;
            filter: blur(76px);
            opacity: 0.26;
            animation: guestFloat 20s ease-in-out infinite;
        }
        .guest-orb-a {
            width: 24rem;
            height: 24rem;
            background: rgba(49, 80, 118, 0.24);
            left: -6rem;
            top: 4rem;
        }
        .guest-orb-b {
            width: 22rem;
            height: 22rem;
            background: rgba(141, 124, 92, 0.2);
            right: -4rem;
            top: 10rem;
            animation-delay: 2.5s;
        }
        .guest-orb-c {
            width: 18rem;
            height: 18rem;
            background: rgba(91, 112, 136, 0.18);
            left: 46%;
            bottom: -6rem;
            animation-delay: 4s;
        }
        .guest-grid-overlay {
            position: absolute;
            inset: 0;
            opacity: 0.11;
            background-image:
                linear-gradient(rgba(15, 23, 42, 0.032) 1px, transparent 1px),
                linear-gradient(90deg, rgba(15, 23, 42, 0.032) 1px, transparent 1px);
            background-size: 34px 34px;
            mask-image: radial-gradient(circle at center, rgba(0, 0, 0, 0.75), transparent 72%);
            -webkit-mask-image: radial-gradient(circle at center, rgba(0, 0, 0, 0.75), transparent 72%);
        }
        .portal-main {
            position: relative;
            z-index: 1;
        }
        .guest-reveal {
            opacity: 0;
            transform: translateY(14px);
            transition: opacity 0.45s ease, transform 0.45s ease;
        }
        .guest-reveal.is-visible {
            opacity: 1;
            transform: translateY(0);
        }
        .guest-glass-nav {
            background: rgba(255, 255, 255, 0.9);
            border-bottom: 1px solid rgba(148, 163, 184, 0.28);
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.07);
            backdrop-filter: blur(10px);
        }
        .guest-nav-cluster {
            scrollbar-width: none;
        }
        .guest-nav-cluster::-webkit-scrollbar {
            display: none;
        }
        .guest-progress-track {
            position: fixed;
            left: 0;
            right: 0;
            top: 0;
            z-index: 40;
            height: 2px;
            background: rgba(11, 31, 58, 0.08);
        }
        .guest-progress-bar {
            height: 100%;
            width: 0;
            background: linear-gradient(90deg, rgba(11, 31, 58, 0.92), rgba(31, 73, 118, 0.88));
            transition: width 120ms linear;
        }
        .guest-page-loading {
            position: fixed;
            inset: 0;
            z-index: 70;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(15, 23, 42, 0.26);
            backdrop-filter: blur(1px);
            opacity: 0;
            transform: scale(1.01);
            pointer-events: none;
            transition: opacity 140ms ease, transform 140ms ease;
        }
        .guest-page-loading.is-visible {
            opacity: 1;
            transform: scale(1);
        }
        .guest-page-loading-card {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            border-radius: 16px;
            border: 1px solid rgba(148, 163, 184, 0.45);
            background: rgba(255, 255, 255, 0.98);
            color: #0f172a;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            padding: 12px 18px;
            box-shadow: 0 14px 36px rgba(15, 23, 42, 0.28);
        }
        .guest-page-loading-spinner {
            width: 22px;
            height: 22px;
            border-radius: 999px;
            border: 3px solid rgba(11, 31, 58, 0.2);
            border-top-color: rgba(11, 31, 58, 0.78);
            animation: guestPageSpin 0.8s linear infinite;
        }
        .guest-toast-stack {
            position: fixed;
            top: 12px;
            right: 12px;
            z-index: 85;
            display: flex;
            flex-direction: column;
            gap: 8px;
            width: min(92vw, 28rem);
            pointer-events: none;
        }
        .guest-toast {
            pointer-events: auto;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            border-radius: 12px;
            border: 1px solid;
            padding: 10px 12px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.18);
        }
        .guest-toast--success {
            border-color: #a7f3d0;
            background: #ecfdf5;
            color: #065f46;
        }
        .guest-toast--error {
            border-color: #fecaca;
            background: #fef2f2;
            color: #991b1b;
        }
        .guest-toast--warning {
            border-color: #fde68a;
            background: #fffbeb;
            color: #92400e;
        }
        .guest-toast--info {
            border-color: #bae6fd;
            background: #f0f9ff;
            color: #0c4a6e;
        }
        .guest-toast-close {
            border: 0;
            background: transparent;
            color: inherit;
            font-size: 16px;
            line-height: 1;
            opacity: 0.75;
            cursor: pointer;
            padding: 0;
        }
        .guest-toast-close:hover {
            opacity: 1;
        }
        @keyframes guestPageSpin {
            to { transform: rotate(360deg); }
        }
        @keyframes guestFloat {
            0%, 100% {
                transform: translateY(0) scale(1);
            }
            50% {
                transform: translateY(-22px) scale(1.04);
            }
        }
        a:focus-visible,
        button:focus-visible,
        input:focus-visible,
        select:focus-visible,
        textarea:focus-visible {
            outline: 2px solid rgba(11, 31, 58, 0.48);
            outline-offset: 2px;
        }
        #google_translate_element select.goog-te-combo {
            display: none;
        }
        .goog-te-banner-frame.skiptranslate,
        .goog-te-menu-frame.skiptranslate,
        .goog-te-balloon-frame.skiptranslate {
            display: none !important;
            visibility: hidden !important;
        }
        /* Keep reCAPTCHA v3 badge visible and interactive on guest pages. */
        .grecaptcha-badge {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            pointer-events: auto !important;
            z-index: 2147483647 !important;
        }
        /* Simple image slider — fixed size and consistent transition */
        .portal-slider {
            position: relative;
        }
        .portal-slider-track {
            position: relative;
            overflow: hidden;
            width: 100%;
            height: clamp(20rem, 45vw, 34rem);/* fixed height so every slide is the same size */
            background: #0f172a;
        }
        .portal-slide {
            position: absolute;
            inset: 0;
            opacity: 0;
            transform: translateX(12px);
            transition: opacity 400ms ease-out, transform 400ms ease-out;
            pointer-events: none;
        }
        .portal-slide.is-active {
            opacity: 1;
            transform: translateX(0);
            pointer-events: auto;
        }
        .portal-slide .portal-slide-image {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        .portal-slider-dot.is-active {
            opacity: 1;
        }
        /* Keep guest navbar search icon crisp across browsers. */
        .guest-search-icon {
            filter: none !important;
            opacity: 0.95;
            stroke-width: 2.2;
            shape-rendering: geometricPrecision;
        }
        #guest-search-input {
            -webkit-appearance: none;
            appearance: none;
        }
        #guest-search-input::-webkit-search-decoration,
        #guest-search-input::-webkit-search-cancel-button,
        #guest-search-input::-webkit-search-results-button,
        #guest-search-input::-webkit-search-results-decoration {
            display: none;
            -webkit-appearance: none;
        }
        /* University Academic Portal logo: hover lift and glow */
        a .portal-logo,
        .portal-logo-link .portal-logo {
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        a:hover .portal-logo,
        a:focus-visible .portal-logo,
        .portal-logo-link:hover .portal-logo {
            transform: scale(1.06);
            box-shadow: 0 4px 14px rgba(11, 31, 58, 0.2), 0 0 0 1px rgba(244, 180, 0, 0.15);
        }
        @media (max-width: 640px) {
            .guest-orb-a,
            .guest-orb-b,
            .guest-orb-c {
                opacity: 0.3;
                filter: blur(56px);
            }
            .guest-grid-overlay {
                opacity: 0.09;
            }
            .guest-nav-cluster {
                display: flex;
                flex-wrap: nowrap;
                overflow-x: auto;
                width: 100%;
                padding-bottom: 0.3rem;
            }
        }
        @media (prefers-reduced-motion: reduce) {
            .guest-orb,
            .guest-reveal,
            .portal-slide {
                animation: none !important;
                transition: none !important;
            }
        }
    </style>
    @stack('styles')
    <!-- Google Translate -->
    <script type="text/javascript">
        window.googleTranslateElementInit = function() {
            const element = document.getElementById('google_translate_element');
            if (element && window.google && window.google.translate) {
                new google.translate.TranslateElement({
                    pageLanguage: 'en',
                    includedLanguages: 'ar,zh-CN,zh-TW,fr,de,hi,id,it,ja,ko,pt,ru,es,th,tr,vi',
                    layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
                    autoDisplay: false
                }, 'google_translate_element');
            }
        };
    </script>
    <script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
</head>
<body class="text-slate-900">
    <div class="guest-shell-bg" aria-hidden="true">
        <span class="guest-orb guest-orb-a"></span>
        <span class="guest-orb guest-orb-b"></span>
        <span class="guest-orb guest-orb-c"></span>
        <span class="guest-grid-overlay"></span>
    </div>
    <div class="guest-progress-track" aria-hidden="true">
        <span id="guest-scroll-progress" class="guest-progress-bar"></span>
    </div>
    <div id="guest-page-loading" class="guest-page-loading" aria-live="polite" role="status">
        <span class="guest-page-loading-card">
            <span class="guest-page-loading-spinner" aria-hidden="true"></span>
            <span>Loading...</span>
        </span>
    </div>
    @if (session('success') || session('error') || session('warning') || session('info'))
        <div class="guest-toast-stack" aria-live="polite" aria-atomic="true">
            @if (session('success'))
                <div class="guest-toast guest-toast--success" data-guest-toast data-timeout="5000" role="status">
                    <span>{{ session('success') }}</span>
                    <button type="button" class="guest-toast-close" data-guest-toast-close aria-label="Dismiss notification">&times;</button>
                </div>
            @endif
            @if (session('error'))
                <div class="guest-toast guest-toast--error" data-guest-toast data-timeout="7000" role="status">
                    <span>{{ session('error') }}</span>
                    <button type="button" class="guest-toast-close" data-guest-toast-close aria-label="Dismiss notification">&times;</button>
                </div>
            @endif
            @if (session('warning'))
                <div class="guest-toast guest-toast--warning" data-guest-toast data-timeout="6500" role="status">
                    <span>{{ session('warning') }}</span>
                    <button type="button" class="guest-toast-close" data-guest-toast-close aria-label="Dismiss notification">&times;</button>
                </div>
            @endif
            @if (session('info'))
                <div class="guest-toast guest-toast--info" data-guest-toast data-timeout="5500" role="status">
                    <span>{{ session('info') }}</span>
                    <button type="button" class="guest-toast-close" data-guest-toast-close aria-label="Dismiss notification">&times;</button>
                </div>
            @endif
        </div>
    @endif
    @php
        $guestRouteName = Route::currentRouteName();

        // Page labels
        $guestCrumbsMap = [
            'guest.home' => 'Home',
            'guest.courses' => 'Courses',
            'guest.courses.show' => 'Course Details',
            'guest.news' => 'News',
            'guest.news.show' => 'Announcement Details',
            'guest.about' => 'About Us',
            'guest.vision' => 'Our Vision',
            'guest.policies' => 'Policies & Guidelines',
            'guest.services' => 'Academic Services',
            'guest.support' => 'Support & Help Desk',
            'guest.user-manual.page' => 'User Manual',
            'guest.feedback' => 'Feedback & Suggestions',
            'guest.contact' => 'Contact',
        ];

        // Section (group) labels to align with dropdown menus
        $guestSection = null;
        if (in_array($guestRouteName, ['guest.about', 'guest.vision', 'guest.policies', 'guest.feedback'])) {
            $guestSection = 'About';
        } elseif (in_array($guestRouteName, ['guest.services', 'guest.support', 'guest.user-manual.page', 'guest.contact'])) {
            $guestSection = 'Help & Services';
        }

        $guestCurrentLabel = $guestCrumbsMap[$guestRouteName] ?? null;
        $guestShowBreadcrumbs = $guestCurrentLabel && $guestRouteName !== 'guest.home';
        $guestActiveLinkClass = 'bg-slate-100 text-[color:var(--portal-navy)] font-semibold';
        $guestInactiveLinkClass = 'text-slate-700 hover:text-[color:var(--portal-navy)] hover:bg-slate-100 transition-colors';
    @endphp

    <nav class="guest-glass-nav sticky top-0 z-30">
        <div class="container mx-auto px-4 py-4 flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('guest.home') }}" class="portal-logo-link flex items-center gap-3 text-xl font-bold text-[color:var(--portal-navy)]">
                @include('guest.partials.portal-logo', ['variant' => 'nav'])
                <span class="tracking-tight">University Academic <span class="text-[color:var(--portal-gold)]">Portal</span></span>
            </a>
            <div class="guest-nav-cluster flex flex-wrap items-center gap-2 text-sm lg:w-auto">
                {{-- Main links --}}
                <a href="{{ route('guest.home') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-full {{ request()->routeIs('guest.home') ? $guestActiveLinkClass : $guestInactiveLinkClass }}" @if(request()->routeIs('guest.home')) aria-current="page" @endif>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span>Home</span>
                </a>
                <a href="{{ route('guest.courses') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-full {{ request()->routeIs('guest.courses*') ? $guestActiveLinkClass : $guestInactiveLinkClass }}" @if(request()->routeIs('guest.courses*')) aria-current="page" @endif>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <span>Courses</span>
                </a>
                <a href="{{ route('guest.news') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-full {{ request()->routeIs('guest.news*') ? $guestActiveLinkClass : $guestInactiveLinkClass }}" @if(request()->routeIs('guest.news*')) aria-current="page" @endif>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                    </svg>
                    <span>News</span>
                </a>

                {{-- About group --}}
                <div class="relative group">
                    <button type="button" class="flex items-center gap-1.5 px-3 py-1.5 rounded-full {{ request()->routeIs('guest.about','guest.vision','guest.policies','guest.feedback') ? $guestActiveLinkClass : $guestInactiveLinkClass }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>About</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="invisible absolute right-0 z-40 top-full w-44 rounded-xl border border-slate-200 bg-white py-2 text-sm text-slate-700 opacity-0 shadow-xl transition group-hover:visible group-hover:opacity-100">
                        <a href="{{ route('guest.about') }}" class="block px-4 py-2 hover:bg-slate-50 {{ request()->routeIs('guest.about') ? 'font-semibold text-[color:var(--portal-navy)]' : '' }}">About Us</a>
                        <a href="{{ route('guest.vision') }}" class="block px-4 py-2 hover:bg-slate-50 {{ request()->routeIs('guest.vision') ? 'font-semibold text-[color:var(--portal-navy)]' : '' }}">Our Vision</a>
                        <a href="{{ route('guest.policies') }}" class="block px-4 py-2 hover:bg-slate-50 {{ request()->routeIs('guest.policies') ? 'font-semibold text-[color:var(--portal-navy)]' : '' }}">Policies & Guidelines</a>
                        <a href="{{ route('guest.feedback') }}" class="block px-4 py-2 hover:bg-slate-50 {{ request()->routeIs('guest.feedback') ? 'font-semibold text-[color:var(--portal-navy)]' : '' }}">Feedback</a>
                    </div>
                </div>

                {{-- Help / Services group --}}
                <div class="relative group">
                    <button type="button" class="flex items-center gap-1.5 px-3 py-1.5 rounded-full {{ request()->routeIs('guest.services','guest.support','guest.user-manual.page','guest.contact') ? $guestActiveLinkClass : $guestInactiveLinkClass }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span>Help & Services</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="invisible absolute right-0 z-40 top-full w-52 rounded-xl border border-slate-200 bg-white py-2 text-sm text-slate-700 opacity-0 shadow-xl transition group-hover:visible group-hover:opacity-100">
                        <a href="{{ route('guest.services') }}" class="block px-4 py-2 hover:bg-slate-50 {{ request()->routeIs('guest.services') ? 'font-semibold text-[color:var(--portal-navy)]' : '' }}">Academic Services</a>
                        <a href="{{ route('guest.support') }}" class="block px-4 py-2 hover:bg-slate-50 {{ request()->routeIs('guest.support') ? 'font-semibold text-[color:var(--portal-navy)]' : '' }}">Support & Help Desk</a>
                        <a href="{{ route('guest.user-manual.page') }}" class="block px-4 py-2 hover:bg-slate-50 {{ request()->routeIs('guest.user-manual.page') ? 'font-semibold text-[color:var(--portal-navy)]' : '' }}">User Manual</a>
                        <a href="{{ route('guest.contact') }}" class="block px-4 py-2 hover:bg-slate-50 {{ request()->routeIs('guest.contact') ? 'font-semibold text-[color:var(--portal-navy)]' : '' }}">Contact Us</a>
                    </div>
                </div>
                {{-- Search everything (guest) --}}
                <div class="relative min-w-[12.5rem] flex-1 sm:flex-none" id="guest-search-wrapper" data-search-url="{{ route('search') }}">
                    <div class="flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-slate-600 transition focus-within:border-[color:var(--portal-navy)] focus-within:bg-white focus-within:ring-2 focus-within:ring-[color:var(--portal-navy)]/20 sm:w-56">
                        <svg class="guest-search-icon h-4 w-4 shrink-0 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input
                            type="search"
                            id="guest-search-input"
                            placeholder="Search everything..."
                            autocomplete="off"
                            class="w-full bg-transparent text-sm outline-none placeholder:text-slate-400"
                            aria-label="Search courses, news, and pages"
                        />
                        <kbd class="hidden shrink-0 rounded bg-slate-200 px-1.5 py-0.5 font-mono text-[10px] text-slate-600 sm:inline-block">Ctrl+K</kbd>
                    </div>
                    <div id="guest-search-dropdown" class="absolute left-0 right-0 top-full z-50 mt-1 max-h-80 overflow-auto rounded-lg border border-slate-200 bg-white py-2 shadow-lg hidden" role="listbox"></div>
                </div>

                {{-- Account group --}}
                @if (Route::has('login') || Route::has('register'))
                    <div class="relative group">
                        <button type="button" class="flex items-center gap-1.5 rounded-full border border-[color:var(--portal-navy)] px-4 py-2 text-sm font-semibold text-[color:var(--portal-navy)] bg-white hover:bg-[color:var(--portal-navy)] hover:text-white transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span>Account</span>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div class="invisible absolute right-0 z-40 top-full w-40 rounded-xl border border-slate-200 bg-white py-2 text-sm text-slate-700 opacity-0 shadow-xl transition group-hover:visible group-hover:opacity-100">
                            @if (Route::has('login'))
                                <a href="{{ route('login') }}" class="block px-4 py-2 hover:bg-slate-50 {{ request()->routeIs('login') ? 'font-semibold text-[color:var(--portal-navy)]' : '' }}">Login</a>
                            @endif
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="block px-4 py-2 hover:bg-slate-50 {{ request()->routeIs('register') ? 'font-semibold text-[color:var(--portal-navy)]' : '' }}">Register</a>
                            @endif
                        </div>
                    </div>
                @endif
                
                {{-- Google Translate Widget --}}
                <div class="relative" id="google-translate-container">
                    <div id="google_translate_element" style="position: absolute; left: -9999px; opacity: 0; pointer-events: none;"></div>
                    <button
                        type="button"
                        id="translate-button"
                        onclick="toggleTranslateDropdown()"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-slate-700 hover:text-[color:var(--portal-navy)] hover:bg-slate-100 transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                        </svg>
                        <span class="hidden sm:inline" id="current-lang">Language</span>
                        <span class="sm:hidden">🌐</span>
                    </button>
                    <div id="translate-dropdown" class="hidden absolute right-0 mt-2 w-56 rounded-lg bg-white shadow-xl border border-slate-200 z-50 max-h-80 overflow-y-auto">
                        <div class="p-2">
                            <button onclick="changeLanguage('en')" class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 rounded-md flex items-center gap-2">
                                <span>🇬🇧</span>
                                <span>English</span>
                            </button>
                            <button onclick="changeLanguage('ar')" class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 rounded-md flex items-center gap-2">
                                <span>🇸🇦</span>
                                <span>العربية</span>
                            </button>
                            <button onclick="changeLanguage('zh-CN')" class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 rounded-md flex items-center gap-2">
                                <span>🇨🇳</span>
                                <span>中文 (简体)</span>
                            </button>
                            <button onclick="changeLanguage('zh-TW')" class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 rounded-md flex items-center gap-2">
                                <span>🇹🇼</span>
                                <span>中文 (繁體)</span>
                            </button>
                            <button onclick="changeLanguage('fr')" class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 rounded-md flex items-center gap-2">
                                <span>🇫🇷</span>
                                <span>Français</span>
                            </button>
                            <button onclick="changeLanguage('de')" class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 rounded-md flex items-center gap-2">
                                <span>🇩🇪</span>
                                <span>Deutsch</span>
                            </button>
                            <button onclick="changeLanguage('hi')" class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 rounded-md flex items-center gap-2">
                                <span>🇮🇳</span>
                                <span>हिन्दी</span>
                            </button>
                            <button onclick="changeLanguage('id')" class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 rounded-md flex items-center gap-2">
                                <span>🇮🇩</span>
                                <span>Bahasa Indonesia</span>
                            </button>
                            <button onclick="changeLanguage('it')" class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 rounded-md flex items-center gap-2">
                                <span>🇮🇹</span>
                                <span>Italiano</span>
                            </button>
                            <button onclick="changeLanguage('ja')" class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 rounded-md flex items-center gap-2">
                                <span>🇯🇵</span>
                                <span>日本語</span>
                            </button>
                            <button onclick="changeLanguage('ko')" class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 rounded-md flex items-center gap-2">
                                <span>🇰🇷</span>
                                <span>한국어</span>
                            </button>
                            <button onclick="changeLanguage('pt')" class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 rounded-md flex items-center gap-2">
                                <span>🇵🇹</span>
                                <span>Português</span>
                            </button>
                            <button onclick="changeLanguage('ru')" class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 rounded-md flex items-center gap-2">
                                <span>🇷🇺</span>
                                <span>Русский</span>
                            </button>
                            <button onclick="changeLanguage('es')" class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 rounded-md flex items-center gap-2">
                                <span>🇪🇸</span>
                                <span>Español</span>
                            </button>
                            <button onclick="changeLanguage('th')" class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 rounded-md flex items-center gap-2">
                                <span>🇹🇭</span>
                                <span>ไทย</span>
                            </button>
                            <button onclick="changeLanguage('tr')" class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 rounded-md flex items-center gap-2">
                                <span>🇹🇷</span>
                                <span>Türkçe</span>
                            </button>
                            <button onclick="changeLanguage('vi')" class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 rounded-md flex items-center gap-2">
                                <span>🇻🇳</span>
                                <span>Tiếng Việt</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    @if ($guestShowBreadcrumbs)
        <div class="border-b border-slate-200 bg-white/70 backdrop-blur">
            <div class="container mx-auto px-4 py-2">
                <nav aria-label="Breadcrumb" class="text-sm">
                    <ol class="flex flex-wrap items-center gap-2 text-slate-600">
                        <li>
                            <a href="{{ route('guest.home') }}" class="font-semibold text-slate-700 hover:text-[color:var(--portal-navy)]">
                                Home
                            </a>
                        </li>
                        @if ($guestSection)
                            <li class="text-slate-400">/</li>
                            <li class="font-semibold text-slate-700">
                                {{ $guestSection }}
                            </li>
                        @endif
                        <li class="text-slate-400">/</li>
                        <li class="font-semibold text-slate-900" aria-current="page">
                            {{ $guestCurrentLabel }}
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    @endif

    <main class="portal-main">
        @yield('content')
    </main>

    <footer class="mt-12 bg-gradient-to-br from-[color:var(--portal-navy)] via-slate-800 to-[color:var(--portal-navy)] text-white">
        <div class="container mx-auto px-4 py-12">
            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-4">
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        @include('guest.partials.portal-logo', ['variant' => 'footer'])
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-wide text-amber-200">University Academic Portal</p>
                            <p class="text-lg font-bold">Academic Excellence</p>
                        </div>
                    </div>
                    <p class="text-sm text-slate-200/80 leading-relaxed">
                        A modern, student-first platform for courses, services, and campus life. Built to be secure, efficient, and accessible for everyone.
                    </p>
                    <div class="flex items-center gap-3 text-sm text-slate-200/90">
                        <svg class="h-5 w-5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>Official office and visit details are available through the Contact page.</span>
                    </div>
                </div>

                <div class="space-y-3">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-amber-200">Explore</h3>
                    <div class="grid grid-cols-1 gap-2 text-sm text-slate-100/90">
                        <a class="hover:text-amber-200 transition" href="{{ route('guest.home') }}">Home</a>
                        <a class="hover:text-amber-200 transition" href="{{ route('guest.courses') }}">Courses</a>
                        <a class="hover:text-amber-200 transition" href="{{ route('guest.news') }}">News</a>
                        <a class="hover:text-amber-200 transition" href="{{ route('guest.about') }}">About</a>
                        <a class="hover:text-amber-200 transition" href="{{ route('guest.vision') }}">Vision</a>
                    </div>
                </div>

                <div class="space-y-3">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-amber-200">Services & Support</h3>
                    <div class="grid grid-cols-1 gap-2 text-sm text-slate-100/90">
                        <a class="hover:text-amber-200 transition" href="{{ route('guest.services') }}">Academic Services</a>
                        <a class="hover:text-amber-200 transition" href="{{ route('guest.support') }}">Support & Help Desk</a>
                        <a class="hover:text-amber-200 transition" href="{{ route('guest.user-manual.page') }}">User Manual</a>
                        <a class="hover:text-amber-200 transition" href="{{ route('guest.contact') }}">Contact</a>
                        <a class="hover:text-amber-200 transition" href="{{ route('guest.policies') }}">Policies & Guidelines</a>
                        <a class="hover:text-amber-200 transition" href="{{ route('guest.feedback') }}">Feedback & Suggestions</a>
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-amber-200">Stay Connected</h3>
                    <p class="text-sm text-slate-200/80">Follow updates, events, and announcements.</p>
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-white/10 opacity-60" title="Facebook profile not configured" aria-hidden="true">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12S0 5.446 0 12.073c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </span>
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-white/10 opacity-60" title="Twitter profile not configured" aria-hidden="true">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                            </svg>
                        </span>
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-white/10 opacity-60" title="LinkedIn profile not configured" aria-hidden="true">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452z"/>
                            </svg>
                        </span>
                    </div>
                    <div class="space-y-2 text-sm text-slate-100/90">
                        <div class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                            </svg>
                            <span>Multi-language supported (Google Translate)</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span>Use the Contact page or Help Desk for assistance.</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-10 flex flex-col gap-4 border-t border-white/10 pt-6 text-sm text-slate-200/80 md:flex-row md:items-center md:justify-between">
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                    <span>Secure | Accessible | Student-first</span>
                </div>
                <div class="flex flex-wrap items-center gap-4">
                    <a class="hover:text-amber-200 transition" href="{{ route('privacy-policy') }}">Privacy Policy</a>
                    <span class="hidden md:block text-white/20">|</span>
                    <a class="hover:text-amber-200 transition" href="{{ route('guest.policies') }}">Academic Policies & Guidelines</a>
                    <span class="hidden md:block text-white/20">|</span>
                    <span>&copy; {{ date('Y') }} University Academic Portal. All rights reserved.</span>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')

    <script>
        // Reading progress indicator for long guest pages.
        document.addEventListener('DOMContentLoaded', function () {
            var progressBar = document.getElementById('guest-scroll-progress');
            if (!progressBar) return;

            function updateProgress() {
                var doc = document.documentElement;
                var scrollTop = doc.scrollTop || document.body.scrollTop;
                var scrollHeight = Math.max(1, doc.scrollHeight - doc.clientHeight);
                var percent = Math.min(100, Math.max(0, (scrollTop / scrollHeight) * 100));
                progressBar.style.width = percent + '%';
            }

            updateProgress();
            window.addEventListener('scroll', updateProgress, { passive: true });
            window.addEventListener('resize', updateProgress);
        });

        // Top-right loading status for guest Blade pages.
        document.addEventListener('DOMContentLoaded', function () {
            var pageLoading = document.getElementById('guest-page-loading');
            if (!pageLoading) return;

            var showTimer = null;
            var isVisible = false;
            var SHOW_DELAY_MS = 120;

            function showLoading() {
                if (isVisible) return;
                if (showTimer) clearTimeout(showTimer);
                showTimer = setTimeout(function () {
                    pageLoading.classList.add('is-visible');
                    isVisible = true;
                }, SHOW_DELAY_MS);
            }

            function hideLoading() {
                if (showTimer) {
                    clearTimeout(showTimer);
                    showTimer = null;
                }
                isVisible = false;
                pageLoading.classList.remove('is-visible');
            }

            function isInternalNavigationLink(link) {
                if (!link) return false;
                if (link.hasAttribute('download')) return false;

                var target = (link.getAttribute('target') || '').toLowerCase();
                if (target === '_blank') return false;

                var href = link.getAttribute('href') || '';
                if (!href || href.charAt(0) === '#') return false;
                if (href.indexOf('mailto:') === 0 || href.indexOf('tel:') === 0 || href.indexOf('javascript:') === 0) {
                    return false;
                }

                var url;
                try {
                    url = new URL(href, window.location.href);
                } catch (e) {
                    return false;
                }

                if (url.origin !== window.location.origin) return false;
                if (url.href === window.location.href) return false;

                return true;
            }

            document.addEventListener('click', function (event) {
                if (event.defaultPrevented) return;
                if (event.button !== 0) return;
                if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;

                var link = event.target.closest('a[href]');
                if (!isInternalNavigationLink(link)) return;

                showLoading();
            }, true);

            document.addEventListener('submit', function (event) {
                if (event.defaultPrevented) return;
                var form = event.target;
                if (!(form instanceof HTMLFormElement)) return;

                var method = (form.getAttribute('method') || 'get').toLowerCase();
                var target = (form.getAttribute('target') || '').toLowerCase();
                if (target === '_blank') return;
                if (method !== 'get' && method !== 'post') return;

                showLoading();
            }, true);

            window.addEventListener('beforeunload', showLoading);
            window.addEventListener('pageshow', hideLoading);
        });

        // Auto-dismiss and close handlers for guest flash toasts.
        document.addEventListener('DOMContentLoaded', function () {
            var toastNodes = document.querySelectorAll('[data-guest-toast]');
            if (!toastNodes.length) return;

            toastNodes.forEach(function (toastNode) {
                var timeoutMs = parseInt(toastNode.getAttribute('data-timeout') || '5000', 10);
                var closeButton = toastNode.querySelector('[data-guest-toast-close]');

                function dismissToast() {
                    if (!toastNode || !toastNode.parentNode) return;
                    toastNode.parentNode.removeChild(toastNode);
                }

                if (closeButton) {
                    closeButton.addEventListener('click', dismissToast);
                }

                window.setTimeout(dismissToast, timeoutMs);
            });
        });

        // Guest search: search everything (courses, news, pages)
        document.addEventListener('DOMContentLoaded', function () {
            var guestSearchWrapper = document.getElementById('guest-search-wrapper');
            if (!guestSearchWrapper) return;
            var searchUrl = guestSearchWrapper.getAttribute('data-search-url');
            var input = document.getElementById('guest-search-input');
            var dropdown = document.getElementById('guest-search-dropdown');
            var minLen = 2;
            var debounceMs = 300;
            var debounceTimer = null;
            var results = [];
            var highlightIndex = -1;
            var typeLabels = { course: 'Course', announcement: 'News', page: 'Page' };

            function fetchResults() {
                var q = (input.value || '').trim();
                if (q.length < minLen) {
                    results = [];
                    dropdown.classList.add('hidden');
                    dropdown.innerHTML = '';
                    return;
                }
                dropdown.classList.remove('hidden');
                dropdown.innerHTML = '<div class="px-4 py-8 text-center text-sm text-slate-500">Searching...</div>';
                fetch(searchUrl + '?q=' + encodeURIComponent(q), {
                    method: 'GET',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin'
                })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        results = data.results || [];
                        highlightIndex = -1;
                        renderResults(q);
                    })
                    .catch(function () {
                        results = [];
                        renderResults(q);
                    });
            }

            function renderResults(q) {
                if (results.length === 0) {
                    dropdown.innerHTML = '<div class="px-4 py-8 text-center text-sm text-slate-500">No results found for "' + (q || '').replace(/</g, '&lt;') + '"</div>';
                    return;
                }
                var html = '<div class="space-y-1" role="listbox">';
                results.forEach(function (r, i) {
                    var typeLabel = typeLabels[r.type] || r.type;
                    var subtitle = r.subtitle ? '<span class="truncate text-xs text-slate-500">' + (r.subtitle || '').replace(/</g, '&lt;') + '</span>' : '';
                    var bg = i === highlightIndex ? ' bg-[color:var(--portal-navy)]/5' : '';
                    html += '<button type="button" class="guest-search-result flex w-full flex-col items-start gap-0.5 px-4 py-2.5 text-left transition hover:bg-slate-50' + bg + '" data-index="' + i + '" data-url="' + (r.url || '').replace(/"/g, '&quot;') + '">';
                    html += '<div class="flex w-full items-center justify-between gap-2"><span class="truncate text-sm font-medium text-slate-900">' + (r.title || '').replace(/</g, '&lt;') + '</span>';
                    html += '<span class="shrink-0 rounded bg-slate-100 px-1.5 py-0.5 text-[10px] font-medium text-slate-600">' + typeLabel + '</span></div>';
                    if (subtitle) html += subtitle;
                    html += '</button>';
                });
                html += '</div>';
                dropdown.innerHTML = html;
                dropdown.querySelectorAll('.guest-search-result').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        var url = btn.getAttribute('data-url');
                        if (url) window.location.href = url;
                    });
                });
            }

            function openDropdown() {
                var q = (input.value || '').trim();
                if (q.length >= minLen && results.length > 0) {
                    dropdown.classList.remove('hidden');
                    renderResults(q);
                } else if (q.length >= minLen) {
                    fetchResults();
                }
            }

            function closeDropdown() {
                dropdown.classList.add('hidden');
                highlightIndex = -1;
            }

            function selectHighlighted() {
                if (highlightIndex >= 0 && results[highlightIndex] && results[highlightIndex].url) {
                    window.location.href = results[highlightIndex].url;
                }
            }

            input.addEventListener('input', function () {
                if (debounceTimer) clearTimeout(debounceTimer);
                var q = (input.value || '').trim();
                if (q.length < minLen) {
                    results = [];
                    dropdown.classList.add('hidden');
                    dropdown.innerHTML = '';
                    return;
                }
                debounceTimer = setTimeout(fetchResults, debounceMs);
            });

            input.addEventListener('focus', openDropdown);
            input.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeDropdown();
                    input.blur();
                    return;
                }
                if (!dropdown.classList.contains('hidden') && results.length > 0) {
                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        highlightIndex = Math.min(highlightIndex + 1, results.length - 1);
                        renderResults((input.value || '').trim());
                        return;
                    }
                    if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        highlightIndex = Math.max(highlightIndex - 1, 0);
                        renderResults((input.value || '').trim());
                        return;
                    }
                    if (e.key === 'Enter' && highlightIndex >= 0) {
                        e.preventDefault();
                        selectHighlighted();
                    }
                }
            });

            document.addEventListener('click', function (e) {
                if (guestSearchWrapper && !guestSearchWrapper.contains(e.target)) closeDropdown();
            });

            document.addEventListener('keydown', function (e) {
                if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                    e.preventDefault();
                    input.focus();
                    openDropdown();
                }
            });
        });

        // Lightweight, reusable slider for guest pages
        document.addEventListener('DOMContentLoaded', function () {
            const sliders = document.querySelectorAll('[data-portal-slider]');
            sliders.forEach((slider) => {
                const slides = Array.from(slider.querySelectorAll('[data-portal-slide]'));
                if (slides.length === 0) return;

                let current = 0;
                const interval = parseInt(slider.dataset.interval || '7000', 10);
                const prefersReducedMotion = window.matchMedia
                    && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                const autoplay = slider.dataset.autoplay === 'true' && !prefersReducedMotion;
                let timer = null;

                const dots = Array.from(slider.querySelectorAll('[data-portal-slider-dot]'));

                function goTo(index) {
                    current = (index + slides.length) % slides.length;
                    slides.forEach((s, i) => {
                        if (i === current) {
                            s.classList.add('is-active');
                        } else {
                            s.classList.remove('is-active');
                        }
                    });
                    dots.forEach((d, i) => {
                        if (i === current) {
                            d.classList.add('is-active');
                        } else {
                            d.classList.remove('is-active');
                        }
                    });
                }

                function next() {
                    goTo(current + 1);
                }

                function prev() {
                    goTo(current - 1);
                }

                const nextBtn = slider.querySelector('[data-portal-slider-next]');
                const prevBtn = slider.querySelector('[data-portal-slider-prev]');

                if (nextBtn) nextBtn.addEventListener('click', () => { next(); restart(); });
                if (prevBtn) prevBtn.addEventListener('click', () => { prev(); restart(); });
                dots.forEach((dot, index) => {
                    dot.addEventListener('click', () => {
                        goTo(index);
                        restart();
                    });
                });

                function start() {
                    if (!autoplay || interval <= 0) return;
                    timer = window.setInterval(next, interval);
                }

                function stop() {
                    if (timer) {
                        window.clearInterval(timer);
                        timer = null;
                    }
                }

                function restart() {
                    stop();
                    start();
                }

                slider.addEventListener('mouseenter', stop);
                slider.addEventListener('mouseleave', start);

                goTo(0);
                start();
            });
        });

        // Reveal major content blocks with a small stagger on scroll.
        document.addEventListener('DOMContentLoaded', function () {
            const revealTargets = Array.from(
                document.querySelectorAll('main section, main article, main .portal-card, main [data-guest-reveal]'),
            );

            revealTargets.forEach((target, index) => {
                target.classList.add('guest-reveal');
                target.style.transitionDelay = `${Math.min(index * 35, 320)}ms`;
            });

            if (!('IntersectionObserver' in window)) {
                revealTargets.forEach((target) => target.classList.add('is-visible'));
                return;
            }

            const observer = new IntersectionObserver(
                (entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('is-visible');
                            observer.unobserve(entry.target);
                        }
                    });
                },
                { threshold: 0.12, rootMargin: '0px 0px -8% 0px' },
            );

            revealTargets.forEach((target) => observer.observe(target));
        });

        // Google Translate Functions
        function toggleTranslateDropdown() {
            const dropdown = document.getElementById('translate-dropdown');
            dropdown.classList.toggle('hidden');
        }

        function changeLanguage(langCode) {
            const select = document.querySelector('.goog-te-combo');
            if (select) {
                // Find the option with matching value
                for (let i = 0; i < select.options.length; i++) {
                    const option = select.options[i];
                    if (option.value === langCode || option.value === `/en/${langCode}`) {
                        select.selectedIndex = i;
                        select.dispatchEvent(new Event('change', { bubbles: true }));
                        document.getElementById('translate-dropdown').classList.add('hidden');
                        updateCurrentLanguage(langCode);
                        return;
                    }
                }
            }

            // Fallback: Set cookie and reload
            if (langCode === 'en') {
                document.cookie = 'googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
            } else {
                const expires = new Date();
                expires.setTime(expires.getTime() + 365 * 24 * 60 * 60 * 1000);
                document.cookie = `googtrans=/en/${langCode};expires=${expires.toUTCString()};path=/`;
            }
            window.location.reload();
        }

        function updateCurrentLanguage(langCode) {
            const langNames = {
                'en': 'English',
                'ar': 'العربية',
                'zh-CN': '中文 (简体)',
                'zh-TW': '中文 (繁體)',
                'fr': 'Français',
                'de': 'Deutsch',
                'hi': 'हिन्दी',
                'id': 'Bahasa Indonesia',
                'it': 'Italiano',
                'ja': '日本語',
                'ko': '한국어',
                'pt': 'Português',
                'ru': 'Русский',
                'es': 'Español',
                'th': 'ไทย',
                'tr': 'Türkçe',
                'vi': 'Tiếng Việt'
            };
            const langName = langNames[langCode] || 'Language';
            document.getElementById('current-lang').textContent = langName;
        }

        // Check current language on page load
        document.addEventListener('DOMContentLoaded', function() {
            const cookie = document.cookie.match(/(?:^|; )googtrans=([^;]*)/);
            if (cookie) {
                const langCode = cookie[1].split('/').pop();
                updateCurrentLanguage(langCode);
            } else {
                updateCurrentLanguage('en');
            }

            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                const container = document.getElementById('google-translate-container');
                const dropdown = document.getElementById('translate-dropdown');
                if (container && dropdown && !container.contains(event.target)) {
                    dropdown.classList.add('hidden');
                }
            });
        });

        // reCAPTCHA v3 for guest forms (contact/feedback).
        document.addEventListener('DOMContentLoaded', function () {
            var recaptchaSiteKey = @json(config('recaptcha.site_key'));
            if (!recaptchaSiteKey) return;

            function hasRecaptchaApi() {
                return !!window.grecaptcha
                    && typeof window.grecaptcha.ready === 'function'
                    && typeof window.grecaptcha.execute === 'function';
            }

            // Trigger once for guest pages so the v3 badge is initialized consistently.
            // Some networks load grecaptcha after DOMContentLoaded, so retry for a while.
            var guestRecaptchaBootstrapped = false;
            function bootstrapGuestPageviewRecaptcha() {
                if (guestRecaptchaBootstrapped || !hasRecaptchaApi()) {
                    return false;
                }

                window.grecaptcha.ready(function () {
                    window.grecaptcha
                        .execute(recaptchaSiteKey, { action: 'guest_pageview' })
                        .then(function (token) {
                            if (token) {
                                guestRecaptchaBootstrapped = true;
                            }
                        })
                        .catch(function () {});
                });

                return true;
            }

            var recaptchaBootstrapAttempts = 0;
            var recaptchaBootstrapTimer = window.setInterval(function () {
                recaptchaBootstrapAttempts += 1;
                if (bootstrapGuestPageviewRecaptcha() || recaptchaBootstrapAttempts >= 150) {
                    window.clearInterval(recaptchaBootstrapTimer);
                }
            }, 200);

            window.addEventListener('load', bootstrapGuestPageviewRecaptcha, { once: true });
            document.addEventListener('visibilitychange', function () {
                if (document.visibilityState === 'visible') {
                    bootstrapGuestPageviewRecaptcha();
                }
            });

            var forms = Array.prototype.slice.call(
                document.querySelectorAll('form[data-recaptcha-action]')
            );
            if (!forms.length) return;

            function hideLoadingOverlay() {
                var loadingNode = document.getElementById('guest-page-loading');
                if (loadingNode) {
                    loadingNode.classList.remove('is-visible');
                }
            }

            function clearRecaptchaError(form) {
                var errorNode = form.querySelector('[data-recaptcha-client-error]');
                if (errorNode) {
                    errorNode.remove();
                }
            }

            function showRecaptchaError(form, message) {
                clearRecaptchaError(form);
                var errorNode = document.createElement('div');
                errorNode.setAttribute('data-recaptcha-client-error', '1');
                errorNode.className = 'rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800';
                errorNode.textContent = message;
                form.prepend(errorNode);
            }

            function setSubmitButtonsState(form, isBusy) {
                var submitButtons = form.querySelectorAll(
                    'button[type="submit"], input[type="submit"]'
                );
                submitButtons.forEach(function (button) {
                    if (isBusy) {
                        button.setAttribute('data-recaptcha-was-disabled', button.disabled ? '1' : '0');
                        button.disabled = true;
                    } else {
                        if (button.getAttribute('data-recaptcha-was-disabled') !== '1') {
                            button.disabled = false;
                        }
                        button.removeAttribute('data-recaptcha-was-disabled');
                    }
                });
            }

            forms.forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (form.getAttribute('data-recaptcha-submitting') === '1') {
                        return;
                    }

                    var tokenInput = form.querySelector('input[name="recaptcha_token"]');
                    if (!tokenInput) {
                        return;
                    }

                    event.preventDefault();
                    clearRecaptchaError(form);
                    setSubmitButtonsState(form, true);

                    var action = form.getAttribute('data-recaptcha-action') || 'submit';

                    function fail(message) {
                        tokenInput.value = '';
                        setSubmitButtonsState(form, false);
                        hideLoadingOverlay();
                        showRecaptchaError(
                            form,
                            message || 'reCAPTCHA verification failed. Please refresh and try again.'
                        );
                    }

                    if (!hasRecaptchaApi()) {
                        fail('reCAPTCHA failed to load. Please refresh the page and try again.');
                        return;
                    }

                    window.grecaptcha.ready(function () {
                        window.grecaptcha
                            .execute(recaptchaSiteKey, { action: action })
                            .then(function (token) {
                                if (!token) {
                                    fail('reCAPTCHA verification failed. Please try again.');
                                    return;
                                }

                                tokenInput.value = token;
                                form.setAttribute('data-recaptcha-submitting', '1');
                                form.submit();
                            })
                            .catch(function () {
                                fail('reCAPTCHA verification failed. Please try again.');
                            });
                    });
                });
            });
        });
    </script>
</body>
</html>
