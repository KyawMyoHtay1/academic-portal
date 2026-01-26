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
                    <p class="text-lg text-slate-700 leading-relaxed mb-6">
                        The University Academic Portal offers a range of services to support academic operations, including student registration, course enrollment, assignment submission, grade viewing, fee management, and academic record administration.
                    </p>
                    <p class="text-lg text-slate-700 leading-relaxed">
                        These services are designed to streamline academic workflows and provide convenient access to essential university resources.
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
        </div>
    </section>
</div>
@endsection
