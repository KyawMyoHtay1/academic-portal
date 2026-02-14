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
            <p class="mx-auto mt-5 max-w-3xl text-base leading-relaxed text-slate-200 md:text-lg">
                Manage registration, courses, grades, fees, timetables, attendance, and communication through one secure and easy-to-use platform.
            </p>
        </div>
    </section>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">Registered Students</p>
            <p class="mt-2 text-3xl font-bold text-blue-900">{{ number_format(data_get($stats, 'totalStudents', 0)) }}</p>
        </div>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Course Enrollments</p>
            <p class="mt-2 text-3xl font-bold text-emerald-900">{{ number_format(data_get($stats, 'totalEnrollments', 0)) }}</p>
        </div>
        <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <p class="text-xs font-semibold uppercase tracking-wide text-indigo-700">Grades Recorded</p>
            <p class="mt-2 text-3xl font-bold text-indigo-900">{{ number_format(data_get($stats, 'totalGrades', 0)) }}</p>
        </div>
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Courses Available</p>
            <p class="mt-2 text-3xl font-bold text-amber-900">{{ number_format(data_get($stats, 'totalCourses', 0)) }}</p>
        </div>
    </section>

    @include('guest.partials.quick-links-strip')

    @include('guest.partials.icon-highlights')

    <section class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md md:p-10">
        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[color:var(--portal-navy)]">Service Overview</p>
        <h2 class="mt-2 text-3xl font-bold text-[color:var(--portal-navy)] md:text-4xl">Built for students, teachers, and academic staff</h2>
        <div class="mt-5 space-y-4 text-sm leading-relaxed text-slate-600 md:text-base">
            <p>
                The portal centralizes critical academic services so every role can complete daily tasks with fewer delays and better visibility.
            </p>
            <p>
                Instead of disconnected systems and manual handoffs, information is synchronized across enrollment, assessment, finance, and scheduling workflows.
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
                <h3 class="text-lg font-semibold text-slate-900">Student Registration</h3>
                <p class="mt-2 text-sm text-slate-600">Streamlined onboarding and verified student profiles.</p>
            </div>
            <div class="service-rise rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <h3 class="text-lg font-semibold text-slate-900">Course Enrollment</h3>
                <p class="mt-2 text-sm text-slate-600">Clear course selection, enrollment tracking, and approvals.</p>
            </div>
            <div class="service-rise rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <h3 class="text-lg font-semibold text-slate-900">Assignments And Grades</h3>
                <p class="mt-2 text-sm text-slate-600">Submission workflows, grade publishing, and progress visibility.</p>
            </div>
            <div class="service-rise rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <h3 class="text-lg font-semibold text-slate-900">Fees And Payments</h3>
                <p class="mt-2 text-sm text-slate-600">Fee status monitoring and payment history in one place.</p>
            </div>
            <div class="service-rise rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <h3 class="text-lg font-semibold text-slate-900">Timetable And Attendance</h3>
                <p class="mt-2 text-sm text-slate-600">Live schedules and attendance capture with report support.</p>
            </div>
            <div class="service-rise rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <h3 class="text-lg font-semibold text-slate-900">Announcements And Messaging</h3>
                <p class="mt-2 text-sm text-slate-600">Timely communication across students, teachers, and staff.</p>
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
                <h3 class="text-lg font-semibold text-slate-900">Students</h3>
                <p class="mt-2 text-sm text-slate-600">View enrollments, grades, fees, announcements, and attendance trends from one personalized dashboard.</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <h3 class="text-lg font-semibold text-slate-900">Teachers</h3>
                <p class="mt-2 text-sm text-slate-600">Manage attendance, assignment grading, class communication, and academic performance oversight for taught subjects.</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <h3 class="text-lg font-semibold text-slate-900">Administrative Staff</h3>
                <p class="mt-2 text-sm text-slate-600">Oversee admissions, approvals, timetables, course structures, and compliance reporting across departments.</p>
            </div>
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md md:p-10">
        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[color:var(--portal-navy)]">Service Flow</p>
        <h2 class="mt-2 text-3xl font-bold text-[color:var(--portal-navy)]">How the portal supports the academic cycle</h2>
        <div class="mt-6 grid gap-4 md:grid-cols-4">
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-700">Step 1</p>
                <p class="mt-1 text-sm font-semibold text-slate-900">Register</p>
                <p class="mt-1 text-xs text-slate-600">Create student records and profile context.</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-700">Step 2</p>
                <p class="mt-1 text-sm font-semibold text-slate-900">Enroll</p>
                <p class="mt-1 text-xs text-slate-600">Map students to courses and subjects.</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-700">Step 3</p>
                <p class="mt-1 text-sm font-semibold text-slate-900">Engage</p>
                <p class="mt-1 text-xs text-slate-600">Track attendance, assignments, grades, and fees.</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-700">Step 4</p>
                <p class="mt-1 text-sm font-semibold text-slate-900">Review</p>
                <p class="mt-1 text-xs text-slate-600">Use records and reports for informed decisions.</p>
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




