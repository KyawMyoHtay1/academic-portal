@extends('layouts.guest')

@section('title', 'Academic Services')

@push('styles')
<style>
    @keyframes serviceRise {
        from {
            opacity: 0;
            transform: translateY(14px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .service-rise {
        animation: serviceRise 0.6s ease-out both;
    }

    .service-rise:nth-child(2) { animation-delay: 0.08s; }
    .service-rise:nth-child(3) { animation-delay: 0.16s; }
    .service-rise:nth-child(4) { animation-delay: 0.24s; }
    .service-rise:nth-child(5) { animation-delay: 0.32s; }
    .service-rise:nth-child(6) { animation-delay: 0.40s; }

    @media (prefers-reduced-motion: reduce) {
        .service-rise {
            animation: none !important;
        }
    }
</style>
@endpush

@section('content')
<div class="container mx-auto max-w-7xl space-y-14 px-4 py-8">
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-900 via-[color:var(--portal-navy)] to-teal-900 px-6 py-14 text-white shadow-2xl ring-2 ring-white/10 md:px-12 md:py-20">
        <div class="absolute -right-20 -top-20 h-72 w-72 rounded-full bg-[color:var(--portal-gold)]/25 blur-3xl"></div>
        <div class="absolute -bottom-20 -left-16 h-72 w-72 rounded-full bg-blue-500/20 blur-3xl"></div>

        <div class="relative z-10 mx-auto max-w-4xl text-center">
            <p class="inline-flex items-center rounded-full bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.14em] text-amber-200 ring-1 ring-white/20">
                Service Hub
            </p>
            <h1 class="mt-4 text-4xl font-bold leading-tight md:text-6xl">
                Academic Services
                <span class="mt-2 block text-transparent bg-gradient-to-r from-[color:var(--portal-gold)] to-amber-300 bg-clip-text">
                    In One Portal
                </span>
            </h1>
            <p class="mx-auto max-w-3xl text-base leading-relaxed text-slate-200 md:text-lg">
                Manage registration, courses, grades, fees, timetables, attendance, and communication through one secure and easy-to-use platform. The goal is to reduce administrative friction while improving data quality and institutional responsiveness at every stage of the academic cycle, from enrollment to graduation.
            </p>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('guest.courses') }}" class="inline-flex items-center rounded-full bg-white px-6 py-3 text-sm font-semibold text-[color:var(--portal-navy)] shadow-md hover:bg-slate-100">
                    Browse Courses
                </a>
                <a href="{{ route('guest.support') }}" class="inline-flex items-center rounded-full border border-white/40 bg-white/10 px-6 py-3 text-sm font-semibold text-white hover:bg-white/20">
                    Get Support
                </a>
            </div>
        </div>
    </section>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-200/80 text-blue-800">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
            </div>
            <p class="mt-3 text-xs font-semibold uppercase tracking-wide text-blue-700">Registered Students</p>
            <p class="mt-1 text-3xl font-bold text-blue-900">{{ number_format(data_get($stats, 'totalStudents', 0)) }}</p>
        </div>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-200/80 text-emerald-800">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
            </div>
            <p class="mt-3 text-xs font-semibold uppercase tracking-wide text-emerald-700">Approved Enrollments</p>
            <p class="mt-1 text-3xl font-bold text-emerald-900">{{ number_format(data_get($stats, 'approvedEnrollments', 0)) }}</p>
        </div>
        <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-200/80 text-indigo-800">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" /></svg>
            </div>
            <p class="mt-3 text-xs font-semibold uppercase tracking-wide text-indigo-700">Grades Recorded</p>
            <p class="mt-1 text-3xl font-bold text-indigo-900">{{ number_format(data_get($stats, 'totalGrades', 0)) }}</p>
        </div>
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-200/80 text-amber-800">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
            </div>
            <p class="mt-3 text-xs font-semibold uppercase tracking-wide text-amber-700">Courses Available</p>
            <p class="mt-1 text-3xl font-bold text-amber-900">{{ number_format(data_get($stats, 'totalCourses', 0)) }}</p>
        </div>
    </section>

    @include('guest.partials.quick-links-strip')

    @include('guest.partials.icon-highlights')

    <section class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md md:p-10">
        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[color:var(--portal-navy)]">Service Overview</p>
        <h2 class="mt-2 text-3xl font-bold text-[color:var(--portal-navy)] md:text-4xl">Built for students, teachers, and academic staff</h2>
        <div class="mt-5 space-y-4 text-sm leading-relaxed text-slate-600 md:text-base">
            <p>
                The portal centralizes critical academic services so every role can complete daily tasks with fewer delays and better visibility into academic operations.
            </p>
            <p>
                Instead of disconnected systems and manual handoffs, information is synchronized across enrollment, assessment, finance, and scheduling workflows. This improves coordination between teams and gives decision-makers a more reliable operational picture.
            </p>
        </div>
    </section>

    @include('guest.partials.image-cards-services')

    <section class="space-y-6">
        <div class="max-w-2xl">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[color:var(--portal-navy)]">Service Areas</p>
            <h2 class="mt-2 text-3xl font-bold text-[color:var(--portal-navy)]">Core academic capabilities</h2>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <div class="service-rise rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-blue-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-slate-900">Student Registration</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">Streamlined onboarding, verified student profiles, and secure account management so new learners can access the portal and their programs without delay.</p>
            </div>
            <div class="service-rise rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-slate-900">Course Enrollment</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">Clear course selection, enrollment tracking, and approval workflows so students and advisors can manage registrations and changes in one place.</p>
            </div>
            <div class="service-rise rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 text-indigo-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-slate-900">Assignments And Grades</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">Submission workflows, grade publishing, and progress visibility so students and teachers can track performance and outcomes throughout the term.</p>
            </div>
            <div class="service-rise rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 text-amber-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2h-2m-4-1H9" /></svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-slate-900">Fees And Payments</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">Fee status monitoring, payment history, and due-date visibility in one place so students and finance teams can stay aligned and avoid surprises.</p>
            </div>
            <div class="service-rise rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-cyan-100 text-cyan-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-slate-900">Timetable And Attendance</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">Live schedules, room and instructor details, and attendance capture with report support so teaching and admin teams have accurate, up-to-date information.</p>
            </div>
            <div class="service-rise rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-violet-100 text-violet-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-slate-900">Announcements And Messaging</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">Timely communication across students, teachers, and staff so important updates, deadlines, and notices reach the right people without clutter or delay.</p>
            </div>
        </div>
    </section>

    <section class="space-y-6">
        <div class="max-w-3xl">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[color:var(--portal-navy)]">Role-Based Experience</p>
            <h2 class="mt-2 text-3xl font-bold text-[color:var(--portal-navy)]">Purpose-built interfaces for each user group</h2>
            <p class="mt-3 text-sm leading-relaxed text-slate-600 md:text-base">
                The portal is structured so each role can focus on relevant tasks without unnecessary complexity. Students track progress, teachers manage delivery, and staff oversee institutional workflows.
            </p>
        </div>
        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-blue-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-slate-900">Students</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">View enrollments, grades, fees, announcements, and attendance trends from one personalized dashboard. Complete submissions, check results, and stay informed throughout the academic year.</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-slate-900">Teachers</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">Manage attendance, assignment grading, class communication, and academic performance oversight for taught subjects, all from a role-specific interface that reduces clutter and speeds up daily tasks.</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 text-indigo-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-slate-900">Administrative Staff</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">Oversee admissions, approvals, timetables, course structures, and compliance reporting across departments with tools designed for institutional scale and auditability.</p>
            </div>
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md md:p-10">
        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[color:var(--portal-navy)]">Service Flow</p>
        <h2 class="mt-2 text-3xl font-bold text-[color:var(--portal-navy)]">How the portal supports the academic cycle</h2>
        <div class="mt-6 grid gap-4 md:grid-cols-4">
            <div class="flex gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-blue-700 font-bold">1</div>
                <div>
                    <p class="text-sm font-semibold text-slate-900">Register</p>
                    <p class="mt-1 text-xs leading-relaxed text-slate-600">Create student records and profile context so each learner has a single, accurate identity across the portal.</p>
                </div>
            </div>
            <div class="flex gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700 font-bold">2</div>
                <div>
                    <p class="text-sm font-semibold text-slate-900">Enroll</p>
                    <p class="mt-1 text-xs leading-relaxed text-slate-600">Map students to courses and subjects with clear approval and capacity rules so enrollments stay consistent.</p>
                </div>
            </div>
            <div class="flex gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-amber-700 font-bold">3</div>
                <div>
                    <p class="text-sm font-semibold text-slate-900">Engage</p>
                    <p class="mt-1 text-xs leading-relaxed text-slate-600">Track attendance, assignments, grades, and fees so students and staff have real-time visibility into progress.</p>
                </div>
            </div>
            <div class="flex gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-indigo-100 text-indigo-700 font-bold">4</div>
                <div>
                    <p class="text-sm font-semibold text-slate-900">Review</p>
                    <p class="mt-1 text-xs leading-relaxed text-slate-600">Use records and reports for informed decisions, quality assurance, and planning the next academic cycle.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-3xl border border-amber-200 bg-gradient-to-r from-amber-100 via-amber-50 to-amber-100 p-8 text-center shadow-md ring-1 ring-amber-200/50 md:p-12">
        <h2 class="text-3xl font-bold text-[color:var(--portal-navy)]">Need support with an academic service?</h2>
        <p class="mx-auto mt-3 max-w-2xl text-sm text-slate-700 md:text-base">
            Contact our team for help with enrollment, grades, scheduling, fees, and portal access.
        </p>
        <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
            <a href="{{ route('guest.support') }}" class="inline-flex items-center rounded-full bg-[color:var(--portal-navy)] px-6 py-3 text-sm font-semibold text-white hover:bg-slate-800">
                Support & Help Desk
            </a>
            <a href="{{ route('guest.contact') }}" class="inline-flex items-center rounded-full border border-[color:var(--portal-navy)] bg-white px-6 py-3 text-sm font-semibold text-[color:var(--portal-navy)] hover:bg-slate-50">
                Contact Us
            </a>
        </div>
    </section>
</div>
@endsection


