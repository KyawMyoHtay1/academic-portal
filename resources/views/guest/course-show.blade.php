@extends('layouts.guest')

@section('title', $course->title . ' - Course Details')

@section('content')
<div class="container mx-auto max-w-7xl px-4 py-6 space-y-8">
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[color:var(--portal-navy)] via-slate-800 to-[color:var(--portal-navy)] px-6 md:px-12 py-12 md:py-16 text-white shadow-2xl">
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-0 right-0 h-80 w-80 rounded-full bg-[color:var(--portal-gold)] mix-blend-multiply blur-3xl"></div>
            <div class="absolute bottom-0 left-0 h-80 w-80 rounded-full bg-blue-500 mix-blend-multiply blur-3xl"></div>
        </div>

        <div class="relative z-10 space-y-5">
            <a href="{{ route('guest.courses') }}" class="inline-flex items-center gap-2 rounded-full border border-white/30 bg-white/10 px-4 py-2 text-sm font-semibold text-white hover:bg-white/20 transition-all">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Courses
            </a>

            <div class="flex flex-wrap items-center gap-3">
                <span class="inline-flex items-center rounded-full bg-white/15 px-3 py-1 text-xs font-bold tracking-wide text-amber-200 ring-1 ring-white/20">
                    {{ $course->course_code }}
                </span>
                <span class="inline-flex items-center rounded-full bg-white/15 px-3 py-1 text-xs font-bold tracking-wide text-slate-100 ring-1 ring-white/20">
                    {{ $course->semester }}
                </span>
            </div>

            <h1 class="text-4xl md:text-5xl font-bold">
                {{ $course->title }}
            </h1>
            <p class="max-w-3xl text-base md:text-lg text-slate-200 leading-relaxed">
                This course is part of the University Academic Portal course catalog. Students can review code, semester, and credit information clearly before enrolment and academic planning.
            </p>
        </div>
    </section>

    <section class="grid gap-6 lg:grid-cols-3">
        <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-md ring-1 ring-slate-900/5 lg:col-span-1">
            <div class="relative h-64 overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200">
                @if ($course->photo)
                    <img
                        src="{{ asset('storage/'.$course->photo) }}"
                        alt="Course photo for {{ $course->title }}"
                        class="h-full w-full object-cover"
                    >
                @else
                    <div class="flex h-full w-full items-center justify-center">
                        <div class="flex h-24 w-24 items-center justify-center rounded-2xl bg-gradient-to-br from-[color:var(--portal-navy)] to-slate-700 text-4xl font-bold text-white shadow-lg">
                            {{ \Illuminate\Support\Str::of($course->title)->substr(0, 1) }}
                        </div>
                    </div>
                @endif
            </div>
            <div class="space-y-3 p-6">
                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Course Summary</p>
                <p class="text-sm leading-relaxed text-slate-600">
                    {{ $course->title }} helps students build core knowledge for semester {{ $course->semester }} with structured credits and timetable alignment.
                </p>
            </div>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-md ring-1 ring-slate-900/5 lg:col-span-2">
            <h2 class="text-2xl font-bold text-slate-900">Course Information</h2>
            <p class="mt-2 text-sm text-slate-600">
                Key details from the University Academic Portal for enrolment planning and academic tracking.
            </p>

            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Course Code</p>
                    <p class="mt-2 text-lg font-bold text-slate-900">{{ $course->course_code }}</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Semester</p>
                    <p class="mt-2 text-lg font-bold text-slate-900">{{ $course->semester }}</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Credits</p>
                    <p class="mt-2 text-lg font-bold text-slate-900">{{ $course->credits }}</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Approved Enrolments</p>
                    <p class="mt-2 text-lg font-bold text-slate-900">{{ number_format($approvedEnrollments) }}</p>
                </div>
            </div>

            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div class="rounded-xl border border-slate-200 bg-white p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Subjects Linked</p>
                    <p class="mt-2 text-base font-bold text-slate-900">{{ number_format($course->subjects_count ?? 0) }}</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Teachers Assigned</p>
                    <p class="mt-2 text-base font-bold text-slate-900">{{ number_format($course->teachers_count ?? 0) }}</p>
                </div>
            </div>
        </article>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-md ring-1 ring-slate-900/5">
        <div class="mb-4 flex items-center justify-between gap-3">
            <h2 class="text-2xl font-bold text-slate-900">Related Courses</h2>
            <a href="{{ route('guest.courses') }}" class="text-sm font-semibold text-[color:var(--portal-navy)] hover:text-slate-900">
                View all courses
            </a>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            @forelse($relatedCourses as $related)
                <article class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $related->course_code }}</p>
                    <h3 class="mt-2 text-base font-bold text-slate-900 line-clamp-2">{{ $related->title }}</h3>
                    <p class="mt-1 text-sm text-slate-600">{{ $related->semester }} - {{ $related->credits }} Cr</p>
                    <a href="{{ route('guest.courses.show', $related->id) }}" class="mt-3 inline-flex items-center gap-1 text-sm font-semibold text-[color:var(--portal-navy)] hover:text-slate-900">
                        View Details
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </article>
            @empty
                <div class="col-span-full rounded-xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center text-slate-600">
                    No related courses available.
                </div>
            @endforelse
        </div>
    </section>
</div>
@endsection

