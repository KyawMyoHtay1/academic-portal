@extends('layouts.guest')

@section('title', 'Welcome to University Portal')

@section('content')
<div class="container mx-auto px-4 py-10 space-y-12">
    {{-- Hero --}}
    <section class="relative overflow-hidden rounded-2xl border border-slate-200 bg-gradient-to-r from-[color:var(--portal-navy)] via-slate-900 to-[color:var(--portal-navy)] px-8 py-12 text-white shadow-xl">
        <div class="absolute inset-0 opacity-10 bg-[radial-gradient(circle_at_20%_20%,white,transparent_35%),radial-gradient(circle_at_80%_0%,white,transparent_25%)]"></div>
        <div class="relative space-y-3">
            <p class="inline-flex rounded-full bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-amber-200 ring-1 ring-white/20">
                Guest access
            </p>
            <h1 class="text-4xl font-bold lg:text-5xl">Welcome to Our University</h1>
            <p class="text-lg text-slate-100">
                Explore our courses, campus, and latest news. Log in or register to access personalised dashboards.
            </p>
            <div class="flex flex-wrap gap-3 pt-2">
                <a href="{{ route('login') }}" class="rounded-full bg-white px-5 py-2 text-sm font-semibold text-[color:var(--portal-navy)] shadow-sm hover:bg-slate-100">
                    Login
                </a>
                @if (Route::has('register'))
                <a href="{{ route('register') }}" class="rounded-full border border-white px-5 py-2 text-sm font-semibold text-white hover:bg-white/10">
                    Register
                </a>
                @endif
            </div>
        </div>
    </section>

    {{-- About + Location --}}
    <section class="grid gap-8 lg:grid-cols-2">
        <div class="space-y-3">
            <h2 class="text-2xl font-semibold text-[color:var(--portal-navy)]">About the University</h2>
            <p class="text-sm text-slate-700 leading-relaxed">
                Our mission is to provide quality education, foster innovation, and prepare students for a successful future.
            </p>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-600">Mission & Vision</p>
                <p class="mt-1 text-sm text-slate-700">
                    Empower learners through research-driven teaching and inclusive academic support.
                </p>
            </div>
        </div>
        <div class="space-y-3">
            <h2 class="text-2xl font-semibold text-[color:var(--portal-navy)]">Location</h2>
            <p class="text-sm text-slate-700">
                123 University Avenue, City, Country. Easily reachable via public transport.
            </p>
            <div class="overflow-hidden rounded-xl border border-slate-200 shadow-sm">
                <iframe
                    class="h-64 w-full"
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3151.8354345093746!2d144.9537363153167!3d-37.81627974202137!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ad65d43f1f1f1f1%3A0xf1f1f1f1f1f1f1f1!2sUniversity!5e0!3m2!1sen!2s!4v1610000000000!5m2!1sen!2s"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                ></iframe>
            </div>
        </div>
    </section>

    {{-- Courses Preview --}}
    <section class="space-y-4">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-[color:var(--portal-navy)]">Courses</p>
                <h2 class="text-2xl font-semibold text-[color:var(--portal-navy)]">Featured Courses</h2>
                <p class="text-sm text-slate-700">Read-only preview for guests.</p>
            </div>
            <a href="{{ route('guest.courses') }}" class="text-[color:var(--portal-navy)] hover:underline text-sm font-semibold">View all</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @forelse($publicCourses as $course)
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-semibold uppercase tracking-wide text-[color:var(--portal-navy)]">{{ $course->course_code }}</p>
                        <span class="rounded-full bg-[color:var(--portal-gold)]/20 px-2 py-0.5 text-[10px] font-semibold text-[color:var(--portal-navy)]">Credits {{ $course->credits }}</span>
                    </div>
                    <h3 class="mt-2 text-lg font-semibold text-slate-900">{{ $course->title }}</h3>
                    <p class="mt-1 text-sm text-slate-600">{{ $course->semester }}</p>
                </div>
            @empty
                <p class="text-sm text-slate-600">No courses to display yet.</p>
            @endforelse
        </div>
    </section>

    {{-- News Preview --}}
    <section class="space-y-4">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-[color:var(--portal-navy)]">Announcements</p>
                <h2 class="text-2xl font-semibold text-[color:var(--portal-navy)]">Latest News</h2>
                <p class="text-sm text-slate-700">Campus updates and public events.</p>
            </div>
            <a href="{{ route('guest.news') }}" class="text-[color:var(--portal-navy)] hover:underline text-sm font-semibold">View all</a>
        </div>
        <ul class="space-y-4">
            @forelse($publicAnnouncements as $news)
                <li class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-3">
                        <h3 class="text-lg font-semibold text-slate-900">{{ $news->title }}</h3>
                        <span class="text-xs text-slate-500">{{ $news->created_at->format('M d, Y') }}</span>
                    </div>
                    <p class="mt-2 text-sm text-slate-700">{{ \Illuminate\Support\Str::limit($news->body, 140) }}</p>
                </li>
            @empty
                <p class="text-sm text-slate-600">No news to display yet.</p>
            @endforelse
        </ul>
    </section>

    {{-- Contact --}}
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="grid gap-4 md:grid-cols-3 md:items-center">
            <div class="md:col-span-2">
                <h2 class="text-xl font-semibold text-[color:var(--portal-navy)]">Contact Us</h2>
                <p class="text-sm text-slate-700">Email: info@university.edu | Phone: +123 456 7890</p>
                <p class="text-sm text-slate-700">Address: 123 University Avenue, City, Country</p>
                <p class="text-sm text-slate-700">
                    Follow us:
                    <a href="#" class="text-[color:var(--portal-navy)] hover:underline">Facebook</a>,
                    <a href="#" class="text-[color:var(--portal-navy)] hover:underline">Twitter</a>,
                    <a href="#" class="text-[color:var(--portal-navy)] hover:underline">Instagram</a>
                </p>
            </div>
            <div class="flex justify-end">
                <a href="{{ route('login') }}" class="rounded-full bg-[color:var(--portal-navy)] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-slate-800">Login</a>
            </div>
        </div>
    </section>
</div>
@endsection

