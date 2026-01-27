@extends('layouts.guest')

@section('title', 'Academic Policies & Guidelines')

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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Policies & Guidelines
            </p>
            <h1 class="text-5xl md:text-6xl lg:text-7xl font-bold leading-tight">
                Academic Policies
                <span class="block text-transparent bg-clip-text bg-gradient-to-r from-[color:var(--portal-gold)] to-amber-300 mt-2">
                    & Guidelines
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                
                <div class="prose prose-lg max-w-none">
                    <h2 class="text-3xl md:text-4xl font-bold text-[color:var(--portal-navy)] mb-6">Academic Policies & Guidelines</h2>
                    <p class="text-lg text-slate-700 leading-relaxed mb-6">
                        This section provides access to official academic policies, regulations, and guidelines that govern university activities. These include assessment rules, grading systems, attendance requirements, examination regulations, and academic integrity policies.
                    </p>
                    <p class="text-lg text-slate-700 leading-relaxed">
                        The portal ensures that all users can easily access up-to-date academic information to support transparency and compliance with university standards.
                    </p>
                </div>
            </div>
        </div>

        {{-- Policy Categories --}}
        <div class="grid gap-6 md:grid-cols-2">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Assessment Rules</h3>
                        <p class="text-slate-600">Guidelines for assignments, projects, and examinations.</p>
                    </div>
                </div>
            </div>
            
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Grading Systems</h3>
                        <p class="text-slate-600">Standardized grading criteria and evaluation methods.</p>
                    </div>
                </div>
            </div>
            
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Attendance Requirements</h3>
                        <p class="text-slate-600">Policies regarding class attendance and participation.</p>
                    </div>
                </div>
            </div>
            
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Academic Integrity</h3>
                        <p class="text-slate-600">Standards for honesty and ethical academic conduct.</p>
                    </div>
                </div>
            </div>

            {{-- Privacy Policy (Termly) - full policy link --}}
            <a href="{{ route('privacy-policy') }}" class="block rounded-2xl border-2 border-slate-200 bg-white p-6 shadow-md hover:shadow-xl hover:border-[color:var(--portal-navy)]/30 transition-all duration-300 hover:-translate-y-2 group">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-[color:var(--portal-navy)] to-slate-700 flex items-center justify-center text-white group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-slate-900 mb-2 group-hover:text-[color:var(--portal-navy)]">Privacy Policy</h3>
                        <p class="text-slate-600 mb-2">Our full Privacy Policy (how we collect, use, and protect your data).</p>
                        <span class="inline-flex items-center gap-1 text-sm font-semibold text-[color:var(--portal-navy)]">
                            View full Privacy Policy
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </span>
                    </div>
                </div>
            </a>
        </div>
    </section>
</div>
@endsection
