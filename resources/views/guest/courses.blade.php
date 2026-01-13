@extends('layouts.guest')

@section('title', 'Courses')

@section('content')
<div class="container mx-auto px-4 py-10 space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-[color:var(--portal-navy)]">All Courses</h1>
        <a href="{{ route('guest.home') }}" class="text-[color:var(--portal-navy)] hover:underline text-sm font-semibold">Back to Home</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($courses as $course)
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                <div class="flex items-start gap-4">
                    <div class="h-16 w-20 overflow-hidden rounded-md border border-slate-200 bg-slate-100 flex items-center justify-center">
                        @if ($course->photo)
                            <img
                                src="{{ asset('storage/'.$course->photo) }}"
                                alt="Course photo for {{ $course->title }}"
                                class="h-full w-full object-cover"
                            >
                        @else
                            <span class="text-xs font-semibold text-slate-500">
                                {{ Str::of($course->title)->substr(0, 1) }}
                            </span>
                        @endif
                    </div>
                    <div class="flex-1 space-y-1">
                        <div class="flex items-center justify-between">
                            <p class="text-xs uppercase font-semibold text-[color:var(--portal-navy)]">{{ $course->course_code }}</p>
                            <span class="rounded-full bg-[color:var(--portal-gold)]/20 px-2 py-0.5 text-[10px] font-semibold text-[color:var(--portal-navy)]">Credits {{ $course->credits }}</span>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900">{{ $course->title }}</h3>
                        <p class="text-sm text-slate-700">Semester: {{ $course->semester }}</p>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-sm text-slate-600">No courses available.</p>
        @endforelse
    </div>
</div>
@endsection

