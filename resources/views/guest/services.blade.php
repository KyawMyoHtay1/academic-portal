@extends('layouts.guest')

@section('title', 'Academic Services')

@push('styles')
<style>
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.8s ease-out;
    }
    .service-card:nth-child(1) { animation-delay: 0.1s; }
    .service-card:nth-child(2) { animation-delay: 0.2s; }
    .service-card:nth-child(3) { animation-delay: 0.3s; }
    .service-card:nth-child(4) { animation-delay: 0.4s; }
    .service-card:nth-child(5) { animation-delay: 0.5s; }
    .service-card:nth-child(6) { animation-delay: 0.6s; }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6 space-y-16">
    {{-- Hero Section --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[color:var(--portal-navy)] via-slate-800 to-[color:var(--portal-navy)] px-6 md:px-12 py-16 md:py-24 text-white shadow-2xl">
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-0 right-0 w-96 h-96 bg-[color:var(--portal-gold)] rounded-full mix-blend-multiply filter blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl"></div>
        </div>
        
        <div class="relative z-10 max-w-4xl mx-auto text-center space-y-6">
            <p class="inline-flex items-center gap-2 rounded-full bg-white/10 backdrop-blur-sm px-4 py-2 text-xs font-semibold uppercase tracking-wide text-amber-200 ring-1 ring-white/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Our Services
            </p>
            <h1 class="text-5xl md:text-6xl lg:text-7xl font-bold leading-tight">
                Academic Services
                <span class="block text-transparent bg-clip-text bg-gradient-to-r from-[color:var(--portal-gold)] to-amber-300 mt-2">
                    Portal Features
                </span>
            </h1>
        </div>
    </section>

    {{-- Services Slider --}}
    <section class="space-y-4">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-[color:var(--portal-navy)] mb-1">Service Highlights</p>
                <h2 class="text-2xl md:text-3xl font-bold text-[color:var(--portal-navy)]">One Portal, Many Services</h2>
                <p class="text-sm md:text-base text-slate-600">Add screenshots or banners that illustrate each service area.</p>
            </div>
        </div>

        <div class="portal-slider rounded-3xl border border-slate-200 bg-slate-900/90 shadow-xl overflow-hidden" data-portal-slider data-autoplay="true" data-interval="6000">
            <div class="portal-slider-track relative">
                <div class="portal-slide is-active" data-portal-slide>
                    <div class="absolute inset-0 overflow-hidden">
                        <div class="portal-slide-image" style="background-image: url('{{ asset('images/services/slide1.png') }}'); background-size: cover; background-position: center;"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/40 to-transparent"></div>
                        <div class="absolute inset-0 flex items-end">
                            <div class="p-6 md:p-8 space-y-1 text-white">
                                <h3 class="text-lg md:text-xl font-bold">Registration & Courses</h3>
                                <p class="text-sm md:text-base text-slate-100/90 max-w-xl">Show how students register, pick courses, and stay organized. Add image: services/slide1.png (1920×640px)</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="portal-slide" data-portal-slide>
                    <div class="absolute inset-0 overflow-hidden">
                        <div class="portal-slide-image" style="background-image: url('{{ asset('images/services/slide2.png') }}'); background-size: cover; background-position: center;"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/40 to-transparent"></div>
                        <div class="absolute inset-0 flex items-end">
                            <div class="p-6 md:p-8 space-y-1 text-white">
                                <h3 class="text-lg md:text-xl font-bold">Grades, Fees, Attendance</h3>
                                <p class="text-sm md:text-base text-slate-100/90 max-w-xl">Highlight grading workflows, fee payment, and attendance tracking. Add image: services/slide2.png (1920×640px)</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="portal-slide" data-portal-slide>
                    <div class="absolute inset-0 overflow-hidden">
                        <div class="portal-slide-image" style="background-image: url('{{ asset('images/services/slide3.png') }}'); background-size: cover; background-position: center;"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/40 to-transparent"></div>
                        <div class="absolute inset-0 flex items-end">
                            <div class="p-6 md:p-8 space-y-1 text-white">
                                <h3 class="text-lg md:text-xl font-bold">Timetables & Notifications</h3>
                                <p class="text-sm md:text-base text-slate-100/90 max-w-xl">Use this to show live timetables, alerts, and announcements. Add image: services/slide3.png (1920×640px)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="absolute inset-x-0 bottom-0 flex items-center justify-between px-4 pb-4">
                <div class="flex gap-2">
                    <button type="button" class="rounded-full bg-black/40 p-2 text-white hover:bg-black/70 transition" data-portal-slider-prev aria-label="Previous slide">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button type="button" class="rounded-full bg-black/40 p-2 text-white hover:bg-black/70 transition" data-portal-slider-next aria-label="Next slide">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" class="portal-slider-dot h-2.5 w-2.5 rounded-full bg-white/80 opacity-50" data-portal-slider-dot aria-label="Go to slide 1"></button>
                    <button type="button" class="portal-slider-dot h-2.5 w-2.5 rounded-full bg-white/80 opacity-50" data-portal-slider-dot aria-label="Go to slide 2"></button>
                    <button type="button" class="portal-slider-dot h-2.5 w-2.5 rounded-full bg-white/80 opacity-50" data-portal-slider-dot aria-label="Go to slide 3"></button>
                </div>
            </div>
        </div>
    </section>

    {{-- Quick links strip (consistent with Home) --}}
    @include('guest.partials.quick-links-strip')

    {{-- Main Content --}}
    <section class="max-w-6xl mx-auto space-y-12">
        <div class="group relative overflow-hidden rounded-3xl border-2 border-slate-200 bg-gradient-to-br from-white to-slate-50 p-8 md:p-12 shadow-xl hover:shadow-2xl transition-all duration-300">
            <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-[color:var(--portal-navy)]/5 to-transparent rounded-full blur-3xl"></div>
            <div class="relative z-10 space-y-6">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-gradient-to-br from-[color:var(--portal-navy)] to-slate-700 text-white shadow-lg">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                
                <div class="prose prose-lg max-w-none">
                    <h2 class="text-3xl md:text-4xl font-bold text-[color:var(--portal-navy)] mb-6">Academic Services</h2>
                    <p class="text-lg text-slate-700 leading-relaxed mb-4">
                        The University Academic Portal brings together the core academic processes in one place, including student registration, course registration, grade submission, fee payment, timetable management, attendance tracking, and communication.
                    </p>
                    <p class="text-lg text-slate-700 leading-relaxed mb-4">
                        Instead of paper forms and manual spreadsheets, each process is handled through a secure and user-friendly web interface that keeps records accurate, up to date, and easy to review for students, teachers, and administrative staff.
                    </p>
                    <p class="text-lg text-slate-700 leading-relaxed">
                        By centralizing these services, the portal reduces errors, saves administrative time, and makes it faster for everyone to find information, follow schedules, and stay informed about important academic activities.
                    </p>
                </div>
            </div>
        </div>

        {{-- Service Cards --}}
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <div class="service-card group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 animate-fade-in-up">
                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-blue-500/10 to-transparent rounded-full blur-2xl"></div>
                <div class="relative z-10">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 text-white mb-4 shadow-lg group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Student Registration</h3>
                    <p class="text-slate-600 mb-2">Easy and efficient student registration process.</p>
                    <div class="text-lg font-bold text-[color:var(--portal-navy)]">
                        {{ $stats['totalStudents'] > 0 ? number_format($stats['totalStudents']) : '0' }} Registered
                    </div>
                </div>
            </div>
            
            <div class="service-card group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 animate-fade-in-up">
                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-green-500/10 to-transparent rounded-full blur-2xl"></div>
                <div class="relative z-10">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-xl bg-gradient-to-br from-green-500 to-green-600 text-white mb-4 shadow-lg group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Course Enrollment</h3>
                    <p class="text-slate-600 mb-2">Browse and enroll in available courses.</p>
                    <div class="text-lg font-bold text-[color:var(--portal-navy)]">
                        {{ $stats['totalEnrollments'] > 0 ? number_format($stats['totalEnrollments']) : '0' }} Enrollments
                    </div>
                </div>
            </div>
            
            <div class="service-card group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 animate-fade-in-up">
                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-purple-500/10 to-transparent rounded-full blur-2xl"></div>
                <div class="relative z-10">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 text-white mb-4 shadow-lg group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Assignment Submission</h3>
                    <p class="text-slate-600 mb-2">Submit assignments online with ease.</p>
                    <div class="text-lg font-bold text-[color:var(--portal-navy)]">
                        {{ $stats['totalSubmissions'] > 0 ? number_format($stats['totalSubmissions']) : '0' }} Submissions
                    </div>
                </div>
            </div>
            
            <div class="service-card group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 animate-fade-in-up">
                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-amber-500/10 to-transparent rounded-full blur-2xl"></div>
                <div class="relative z-10">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 text-white mb-4 shadow-lg group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Grade Viewing</h3>
                    <p class="text-slate-600 mb-2">Access your grades and academic performance.</p>
                    <div class="text-lg font-bold text-[color:var(--portal-navy)]">
                        {{ $stats['totalGrades'] > 0 ? number_format($stats['totalGrades']) : '0' }} Grades Recorded
                    </div>
                </div>
            </div>
            
            <div class="service-card group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 animate-fade-in-up">
                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-red-500/10 to-transparent rounded-full blur-2xl"></div>
                <div class="relative z-10">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-xl bg-gradient-to-br from-red-500 to-red-600 text-white mb-4 shadow-lg group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Fee Management</h3>
                    <p class="text-slate-600 mb-2">View and manage tuition fees and payments.</p>
                    <div class="text-lg font-bold text-[color:var(--portal-navy)]">
                        {{ $stats['paidFees'] > 0 ? number_format($stats['paidFees']) : '0' }}/{{ $stats['totalFees'] > 0 ? number_format($stats['totalFees']) : '0' }} Paid
                    </div>
                </div>
            </div>
            
            <div class="service-card group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 animate-fade-in-up">
                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-indigo-500/10 to-transparent rounded-full blur-2xl"></div>
                <div class="relative z-10">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 text-white mb-4 shadow-lg group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Academic Records</h3>
                    <p class="text-slate-600 mb-2">Access your complete academic history.</p>
                    <div class="text-lg font-bold text-[color:var(--portal-navy)]">
                        {{ $stats['totalCourses'] > 0 ? number_format($stats['totalCourses']) : '0' }} Courses Available
                    </div>
                </div>
            </div>

            {{-- Timetable Management --}}
            <div class="service-card group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 animate-fade-in-up">
                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-teal-500/10 to-transparent rounded-full blur-2xl"></div>
                <div class="relative z-10">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-xl bg-gradient-to-br from-teal-500 to-teal-600 text-white mb-4 shadow-lg group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M5 11h14M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Timetable Management</h3>
                    <p class="text-slate-600">
                        Automatically manages class and teacher timetables based on course enrolments and room availability, so everyone always sees the latest schedule without manual spreadsheet updates.
                    </p>
                </div>
            </div>

            {{-- Attendance Tracking --}}
            <div class="service-card group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 animate-fade-in-up">
                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-emerald-500/10 to-transparent rounded-full blur-2xl"></div>
                <div class="relative z-10">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 text-white mb-4 shadow-lg group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Attendance Tracking</h3>
                    <p class="text-slate-600">
                        Lets teachers record attendance online and automatically builds reports, helping staff monitor attendance patterns and students keep track of their own attendance status.
                    </p>
                </div>
            </div>

            {{-- Communication & Notifications --}}
            <div class="service-card group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 animate-fade-in-up">
                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-sky-500/10 to-transparent rounded-full blur-2xl"></div>
                <div class="relative z-10">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-xl bg-gradient-to-br from-sky-500 to-sky-600 text-white mb-4 shadow-lg group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Communication & Notifications</h3>
                    <p class="text-slate-600">
                        Centralizes announcements and notifications for exams, schedules, events, and policy updates so that students, teachers, and staff receive clear, timely information in one place.
                    </p>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
