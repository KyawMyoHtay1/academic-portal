<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'University Portal')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --portal-navy: #0b1f3a;
            --portal-gold: #f4b400;
        }
    </style>
</head>
<body class="bg-gradient-to-b from-slate-50 via-white to-slate-100 font-sans text-slate-900">
    <nav class="sticky top-0 z-30 bg-white/90 backdrop-blur shadow-sm border-b border-slate-200">
        <div class="container mx-auto px-4 py-4 flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('guest.home') }}" class="flex items-center gap-2 text-xl font-bold text-[color:var(--portal-navy)]">
                <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-[color:var(--portal-gold)] text-[color:var(--portal-navy)] shadow-inner">UA</span>
                <span>University Portal</span>
            </a>
            <div class="flex flex-wrap items-center gap-2 text-sm">
                <a href="{{ route('guest.home') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-slate-700 hover:text-[color:var(--portal-navy)] hover:bg-slate-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span>Home</span>
                </a>
                <a href="{{ route('guest.courses') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-slate-700 hover:text-[color:var(--portal-navy)] hover:bg-slate-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <span>Courses</span>
                </a>
                <a href="{{ route('guest.news') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-slate-700 hover:text-[color:var(--portal-navy)] hover:bg-slate-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                    </svg>
                    <span>News</span>
                </a>
                <a href="{{ route('guest.about') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-slate-700 hover:text-[color:var(--portal-navy)] hover:bg-slate-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>About</span>
                </a>
                <a href="{{ route('guest.vision') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-slate-700 hover:text-[color:var(--portal-navy)] hover:bg-slate-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <span>Vision</span>
                </a>
                <a href="{{ route('guest.services') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-slate-700 hover:text-[color:var(--portal-navy)] hover:bg-slate-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span>Services</span>
                </a>
                <a href="{{ route('guest.support') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-slate-700 hover:text-[color:var(--portal-navy)] hover:bg-slate-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span>Support</span>
                </a>
                <a href="{{ route('guest.contact') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-slate-700 hover:text-[color:var(--portal-navy)] hover:bg-slate-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span>Contact</span>
                </a>
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="flex items-center gap-1.5 rounded-full bg-[color:var(--portal-navy)] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        <span>Login</span>
                    </a>
                @endif
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="flex items-center gap-1.5 rounded-full border border-[color:var(--portal-navy)] px-4 py-2 text-sm font-semibold text-[color:var(--portal-navy)] hover:bg-[color:var(--portal-navy)] hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        <span>Register</span>
                    </a>
                @endif
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="bg-white border-t border-slate-200 shadow-inner mt-12 py-6">
        <div class="container mx-auto text-center text-slate-600 text-sm">
            &copy; {{ date('Y') }} University Portal. All rights reserved.
        </div>
    </footer>
</body>
</html>

