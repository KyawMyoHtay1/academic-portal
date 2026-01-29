@extends('layouts.guest')

@section('title', 'Our Vision')

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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Our Vision
            </p>
            <h1 class="text-5xl md:text-6xl lg:text-7xl font-bold leading-tight">
                Shaping the Future of
                <span class="block text-transparent bg-clip-text bg-gradient-to-r from-[color:var(--portal-gold)] to-amber-300 mt-2">
                    Academic Excellence
                </span>
            </h1>
        </div>
    </section>

    {{-- Main Content --}}
    <section class="max-w-4xl mx-auto space-y-8">
        <div class="group relative overflow-hidden rounded-3xl border-2 border-slate-200 bg-gradient-to-br from-white to-slate-50 p-8 md:p-12 shadow-xl hover:shadow-2xl transition-all duration-300">
            <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-[color:var(--portal-navy)]/5 to-transparent rounded-full blur-3xl"></div>
            <div class="relative z-10 space-y-6">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-gradient-to-br from-[color:var(--portal-navy)] to-slate-700 text-white shadow-lg">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                
                <div class="prose prose-lg max-w-none">
                    <h2 class="text-3xl md:text-4xl font-bold text-[color:var(--portal-navy)] mb-6">Our Vision</h2>
                    <p class="text-lg text-slate-700 leading-relaxed mb-6">
                        The University Academic Portal aims to provide a secure, efficient, and user-friendly digital platform that supports students, lecturers, and administrative staff in managing academic activities.
                    </p>
                    <p class="text-lg text-slate-700 leading-relaxed">
                        Our vision is to enhance academic processes through technology by reducing paperwork, improving communication, and ensuring accurate record management, thereby supporting academic excellence and institutional efficiency.
                    </p>
                </div>
            </div>
        </div>

        {{-- Key Objectives with Statistics --}}
        <div class="grid gap-6 md:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 text-white mb-4 shadow-lg">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Security First</h3>
                <p class="text-slate-600 mb-3">Ensuring data protection and secure access for all users.</p>
                <div class="text-2xl font-bold text-[color:var(--portal-navy)]">
                    {{ $stats['totalUsers'] > 0 ? number_format($stats['totalUsers']) . '+' : '0' }} Users
                </div>
            </div>
            
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-xl bg-gradient-to-br from-green-500 to-green-600 text-white mb-4 shadow-lg">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Efficiency</h3>
                <p class="text-slate-600 mb-3">Streamlining processes to save time and resources.</p>
                <div class="text-2xl font-bold text-[color:var(--portal-navy)]">
                    {{ $stats['totalEnrollments'] > 0 ? number_format($stats['totalEnrollments']) : '0' }} Enrollments
                </div>
            </div>
            
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 text-white mb-4 shadow-lg">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">User-Friendly</h3>
                <p class="text-slate-600 mb-3">Intuitive design for seamless user experience.</p>
                <div class="text-2xl font-bold text-[color:var(--portal-navy)]">
                    {{ $stats['totalCourses'] > 0 ? number_format($stats['totalCourses']) : '0' }} Courses
                </div>
            </div>
        </div>
    </section>

    {{-- Future Focus Areas --}}
    <section class="space-y-8">
        <div class="text-center max-w-3xl mx-auto">
            <p class="text-xs font-semibold uppercase tracking-wide text-[color:var(--portal-navy)] mb-3">
                Looking Ahead
            </p>
            <h2 class="text-3xl md:text-4xl font-bold text-[color:var(--portal-navy)] mb-3">
                Where Our Vision Leads Us
            </h2>
            <p class="text-lg text-slate-600">
                Our long-term vision is grounded in a practical roadmap that strengthens teaching, technology, and community.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="animate-fade-in-up group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:shadow-2xl hover:-translate-y-2 transition-all duration-300">
                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-[color:var(--portal-navy)]/10 to-transparent rounded-full blur-2xl"></div>
                <div class="relative z-10 space-y-3">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-xl bg-gradient-to-br from-[color:var(--portal-navy)] to-slate-700 text-white shadow-lg group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-3-9l3 3-3 3m3 3l-3 3 3 3M6 9l3-3-3-3m0 12l3 3-3 3"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900">Digital Innovation</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Continuously improving the portal with secure, scalable technologies, analytics, and integrations
                        that support smarter decision‑making and richer learning experiences.
                    </p>
                </div>
            </div>

            <div class="animate-fade-in-up group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:shadow-2xl hover:-translate-y-2 transition-all duration-300">
                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-emerald-500/10 to-transparent rounded-full blur-2xl"></div>
                <div class="relative z-10 space-y-3">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 text-white shadow-lg group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2 0 1.105.843 2 1.88 2.21L12 12.5l1.12-.29C14.157 12 15 11.105 15 10c0-1.105-1.343-2-3-2zm0 5c-2.21 0-4 1.12-4 2.5V18h8v-2.5C16 14.12 14.21 13 12 13z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900">Personalised Experience</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Building dashboards and tools that adapt to each user’s role—so students, lecturers, and staff
                        see the information and actions that matter most to them.
                    </p>
                </div>
            </div>

            <div class="animate-fade-in-up group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:shadow-2xl hover:-translate-y-2 transition-all duration-300">
                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-[color:var(--portal-gold)]/10 to-transparent rounded-full blur-2xl"></div>
                <div class="relative z-10 space-y-3">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-xl bg-gradient-to-br from-[color:var(--portal-gold)] to-amber-500 text-[color:var(--portal-navy)] shadow-lg group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900">Global Reach</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Supporting international partnerships, online learning, and collaborative projects so that the
                        university community can connect and contribute globally.
                    </p>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
