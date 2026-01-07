@extends('layouts.guest')

@section('title', 'Welcome to University Portal')

@section('content')
<div class="container mx-auto px-4 py-10 space-y-12">
    {{-- Hero --}}
    <section class="text-center space-y-3">
        <h1 class="text-4xl font-bold text-gray-900">Welcome to Our University</h1>
        <p class="text-lg text-gray-700">Explore our courses, campus, and latest news.</p>
    </section>

    {{-- About --}}
    <section class="space-y-3">
        <h2 class="text-2xl font-semibold text-gray-900">About the University</h2>
        <p class="text-gray-700 leading-relaxed">
            Our mission is to provide quality education, foster innovation, and prepare students for a successful future.
        </p>
    </section>

    {{-- Courses Preview --}}
    <section class="space-y-4">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-2xl font-semibold text-gray-900">Featured Courses</h2>
            <a href="{{ route('guest.courses') }}" class="text-blue-600 hover:underline text-sm">View all</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @forelse($publicCourses as $course)
                <div class="border rounded-lg p-4 shadow-sm hover:shadow-md transition">
                    <h3 class="text-lg font-bold text-gray-900 mb-1">{{ $course->title }} ({{ $course->course_code }})</h3>
                    <p class="text-gray-700 text-sm">Credits: {{ $course->credits }} | {{ $course->semester }}</p>
                </div>
            @empty
                <p class="text-sm text-gray-600">No courses to display yet.</p>
            @endforelse
        </div>
    </section>

    {{-- News Preview --}}
    <section class="space-y-4">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-2xl font-semibold text-gray-900">Latest News</h2>
            <a href="{{ route('guest.news') }}" class="text-blue-600 hover:underline text-sm">View all</a>
        </div>
        <ul class="space-y-4">
            @forelse($publicAnnouncements as $news)
                <li class="border-l-4 border-blue-600 pl-4">
                    <h3 class="text-lg font-bold text-gray-900">{{ $news->title }}</h3>
                    <p class="text-gray-700 text-sm">{{ \Illuminate\Support\Str::limit($news->body, 140) }}</p>
                    <span class="text-gray-500 text-xs">{{ $news->created_at->format('M d, Y') }}</span>
                </li>
            @empty
                <p class="text-sm text-gray-600">No news to display yet.</p>
            @endforelse
        </ul>
    </section>

    {{-- Contact --}}
    <section class="text-center space-y-2">
        <h2 class="text-2xl font-semibold text-gray-900">Contact Us</h2>
        <p class="text-gray-700">Email: info@university.edu | Phone: +123 456 7890</p>
        <p class="text-gray-700">
            Follow us:
            <a href="#" class="text-blue-600 hover:underline">Facebook</a>,
            <a href="#" class="text-blue-600 hover:underline">Twitter</a>,
            <a href="#" class="text-blue-600 hover:underline">Instagram</a>
        </p>
    </section>
</div>
@endsection

