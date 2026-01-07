@extends('layouts.guest')

@section('title', 'Courses')

@section('content')
<div class="container mx-auto px-4 py-10 space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-900">All Courses</h1>
        <a href="{{ route('guest.home') }}" class="text-blue-600 hover:underline text-sm">Back to Home</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($courses as $course)
            <div class="border rounded-lg p-4 shadow-sm hover:shadow-md transition">
                <p class="text-xs uppercase font-semibold text-blue-700">{{ $course->course_code }}</p>
                <h3 class="text-lg font-bold text-gray-900 mt-1">{{ $course->title }}</h3>
                <p class="text-sm text-gray-700 mt-2">Credits: {{ $course->credits }}</p>
                <p class="text-sm text-gray-700">Semester: {{ $course->semester }}</p>
            </div>
        @empty
            <p class="text-sm text-gray-600">No courses available.</p>
        @endforelse
    </div>
</div>
@endsection

