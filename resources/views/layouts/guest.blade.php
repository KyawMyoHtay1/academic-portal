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
                <a href="{{ route('guest.home') }}" class="px-3 py-1.5 rounded-full text-slate-700 hover:text-[color:var(--portal-navy)] hover:bg-slate-100">Home</a>
                <a href="{{ route('guest.courses') }}" class="px-3 py-1.5 rounded-full text-slate-700 hover:text-[color:var(--portal-navy)] hover:bg-slate-100">Courses</a>
                <a href="{{ route('guest.news') }}" class="px-3 py-1.5 rounded-full text-slate-700 hover:text-[color:var(--portal-navy)] hover:bg-slate-100">News</a>
                <a href="{{ route('guest.about') }}" class="px-3 py-1.5 rounded-full text-slate-700 hover:text-[color:var(--portal-navy)] hover:bg-slate-100">About</a>
                <a href="{{ route('guest.vision') }}" class="px-3 py-1.5 rounded-full text-slate-700 hover:text-[color:var(--portal-navy)] hover:bg-slate-100">Vision</a>
                <a href="{{ route('guest.services') }}" class="px-3 py-1.5 rounded-full text-slate-700 hover:text-[color:var(--portal-navy)] hover:bg-slate-100">Services</a>
                <a href="{{ route('guest.support') }}" class="px-3 py-1.5 rounded-full text-slate-700 hover:text-[color:var(--portal-navy)] hover:bg-slate-100">Support</a>
                <a href="{{ route('guest.contact') }}" class="px-3 py-1.5 rounded-full text-slate-700 hover:text-[color:var(--portal-navy)] hover:bg-slate-100">Contact</a>
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="rounded-full bg-[color:var(--portal-navy)] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-slate-800">Login</a>
                @endif
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="rounded-full border border-[color:var(--portal-navy)] px-4 py-2 text-sm font-semibold text-[color:var(--portal-navy)] hover:bg-[color:var(--portal-navy)] hover:text-white">Register</a>
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

