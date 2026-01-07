<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'University Portal')</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.2/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 font-sans text-gray-800">
    <nav class="bg-white shadow">
        <div class="container mx-auto px-4 py-4 flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('guest.home') }}" class="text-xl font-bold text-blue-700">University Portal</a>
            <div class="flex flex-wrap items-center gap-3 text-sm">
                <a href="{{ route('guest.home') }}" class="text-gray-700 hover:text-blue-700">Home</a>
                <a href="{{ route('guest.courses') }}" class="text-gray-700 hover:text-blue-700">Courses</a>
                <a href="{{ route('guest.news') }}" class="text-gray-700 hover:text-blue-700">News</a>
                <a href="{{ route('guest.about') }}" class="text-gray-700 hover:text-blue-700">About</a>
                <a href="{{ route('guest.contact') }}" class="text-gray-700 hover:text-blue-700">Contact</a>
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="bg-blue-600 text-white px-4 py-1.5 rounded hover:bg-blue-700">Login</a>
                @endif
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="bg-gray-200 text-gray-800 px-4 py-1.5 rounded hover:bg-gray-300">Register</a>
                @endif
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="bg-white shadow mt-12 py-6">
        <div class="container mx-auto text-center text-gray-600 text-sm">
            &copy; {{ date('Y') }} University Portal. All rights reserved.
        </div>
    </footer>
</body>
</html>

