<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'University Portal')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @if (config('recaptcha.site_key'))
        <!-- Google reCAPTCHA v3 -->
        <script src="https://www.google.com/recaptcha/api.js?render={{ config('recaptcha.site_key') }}"></script>
    @endif

    <style>
        :root {
            --portal-navy: #0b1f3a;
            --portal-gold: #f4b400;
        }
        /* Hide Google Translate's default UI */
        .goog-te-banner-frame,
        .goog-te-menu-value,
        .goog-te-menu-frame {
            display: none !important;
        }
        body {
            top: 0 !important;
        }
        #google_translate_element select.goog-te-combo {
            display: none;
        }
        .skiptranslate {
            display: none !important;
        }
        /* Simple image slider — all slides stay absolute so they stack and size consistently */
        .portal-slider {
            position: relative;
        }
        .portal-slider-track {
            position: relative;
            overflow: hidden;
            min-height: 14rem;
        }
        .portal-slide {
            position: absolute;
            inset: 0;
            opacity: 0;
            transform: translateX(12px);
            transition: opacity 500ms ease, transform 500ms ease;
            pointer-events: none;
        }
        .portal-slide.is-active {
            opacity: 1;
            transform: translateX(0);
            pointer-events: auto;
        }
        .portal-slider-dot.is-active {
            opacity: 1;
        }
    </style>
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
<body class="bg-gradient-to-b from-slate-50 via-white to-slate-100 font-sans text-slate-900">
    @php
        $guestRouteName = Route::currentRouteName();

        // Page labels
        $guestCrumbsMap = [
            'guest.home' => 'Home',
            'guest.courses' => 'Courses',
            'guest.news' => 'News',
            'guest.about' => 'About Us',
            'guest.vision' => 'Our Vision',
            'guest.policies' => 'Policies & Guidelines',
            'guest.services' => 'Academic Services',
            'guest.support' => 'Support & Help Desk',
            'guest.feedback' => 'Feedback & Suggestions',
            'guest.contact' => 'Contact',
        ];

        // Section (group) labels to align with dropdown menus
        $guestSection = null;
        if (in_array($guestRouteName, ['guest.about', 'guest.vision', 'guest.policies', 'guest.feedback'])) {
            $guestSection = 'About';
        } elseif (in_array($guestRouteName, ['guest.services', 'guest.support', 'guest.contact'])) {
            $guestSection = 'Help & Services';
        }

        $guestCurrentLabel = $guestCrumbsMap[$guestRouteName] ?? null;
        $guestShowBreadcrumbs = $guestCurrentLabel && $guestRouteName !== 'guest.home';
        $guestActiveLinkClass = 'bg-slate-100 text-[color:var(--portal-navy)] font-semibold';
        $guestInactiveLinkClass = 'text-slate-700 hover:text-[color:var(--portal-navy)] hover:bg-slate-100 transition-colors';
    @endphp

    <nav class="sticky top-0 z-30 bg-white/90 backdrop-blur shadow-sm border-b border-slate-200">
        <div class="container mx-auto px-4 py-4 flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('guest.home') }}" class="flex items-center gap-2 text-xl font-bold text-[color:var(--portal-navy)]">
                <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-[color:var(--portal-gold)] text-[color:var(--portal-navy)] shadow-inner">UA</span>
                <span>University Portal</span>
            </a>
            <div class="flex flex-wrap items-center gap-2 text-sm">
                {{-- Main links --}}
                <a href="{{ route('guest.home') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-full {{ request()->routeIs('guest.home') ? $guestActiveLinkClass : $guestInactiveLinkClass }}" @if(request()->routeIs('guest.home')) aria-current="page" @endif>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span>Home</span>
                </a>
                <a href="{{ route('guest.courses') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-full {{ request()->routeIs('guest.courses') ? $guestActiveLinkClass : $guestInactiveLinkClass }}" @if(request()->routeIs('guest.courses')) aria-current="page" @endif>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <span>Courses</span>
                </a>
                <a href="{{ route('guest.news') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-full {{ request()->routeIs('guest.news') ? $guestActiveLinkClass : $guestInactiveLinkClass }}" @if(request()->routeIs('guest.news')) aria-current="page" @endif>
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
                    <button type="button" class="flex items-center gap-1.5 px-3 py-1.5 rounded-full {{ request()->routeIs('guest.services','guest.support','guest.contact') ? $guestActiveLinkClass : $guestInactiveLinkClass }}">
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
                        <a href="{{ route('guest.contact') }}" class="block px-4 py-2 hover:bg-slate-50 {{ request()->routeIs('guest.contact') ? 'font-semibold text-[color:var(--portal-navy)]' : '' }}">Contact Us</a>
                    </div>
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

    <main>
        @yield('content')
    </main>

    <footer class="mt-12 bg-gradient-to-br from-[color:var(--portal-navy)] via-slate-800 to-[color:var(--portal-navy)] text-white">
        <div class="container mx-auto px-4 py-12">
            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-4">
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-[color:var(--portal-gold)] text-[color:var(--portal-navy)] text-lg font-bold shadow-inner">UA</span>
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-wide text-amber-200">University Portal</p>
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
                        <span>123 University Avenue, City, Country</span>
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
                        <a class="hover:text-amber-200 transition" href="{{ route('guest.contact') }}">Contact</a>
                        <a class="hover:text-amber-200 transition" href="{{ route('guest.policies') }}">Policies & Guidelines</a>
                        <a class="hover:text-amber-200 transition" href="{{ route('guest.feedback') }}">Feedback & Suggestions</a>
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-amber-200">Stay Connected</h3>
                    <p class="text-sm text-slate-200/80">Follow updates, events, and announcements.</p>
                    <div class="flex flex-wrap items-center gap-3">
                        <a href="#" class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-white/10 hover:bg-white/20 transition" aria-label="Facebook">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12S0 5.446 0 12.073c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        <a href="#" class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-white/10 hover:bg-white/20 transition" aria-label="Twitter">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                            </svg>
                        </a>
                        <a href="#" class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-white/10 hover:bg-white/20 transition" aria-label="LinkedIn">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452z"/>
                            </svg>
                        </a>
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
                            <span>Contact: info@university.edu | +123 456 7890</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-10 flex flex-col gap-4 border-t border-white/10 pt-6 text-sm text-slate-200/80 md:flex-row md:items-center md:justify-between">
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                    <span>Secure · Accessible · Student-first</span>
                </div>
                <div class="flex flex-wrap items-center gap-4">
                    <a class="hover:text-amber-200 transition" href="{{ route('privacy-policy') }}">Privacy Policy</a>
                    <span class="hidden md:block text-white/20">|</span>
                    <a class="hover:text-amber-200 transition" href="{{ route('guest.policies') }}">Academic Policies & Guidelines</a>
                    <span class="hidden md:block text-white/20">|</span>
                    <span>&copy; {{ date('Y') }} University Portal. All rights reserved.</span>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')

    <script>
        // Lightweight, reusable slider for guest pages
        document.addEventListener('DOMContentLoaded', function () {
            const sliders = document.querySelectorAll('[data-portal-slider]');
            sliders.forEach((slider) => {
                const slides = Array.from(slider.querySelectorAll('[data-portal-slide]'));
                if (slides.length === 0) return;

                let current = 0;
                const interval = parseInt(slider.dataset.interval || '7000', 10);
                const autoplay = slider.dataset.autoplay === 'true';
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
    </script>
</body>
</html>

