@extends('layouts.guest')

@section('title', 'Feedback & Suggestions')

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
    .form-input:focus {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.1);
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                </svg>
                Your Voice Matters
            </p>
            <h1 class="text-5xl md:text-6xl lg:text-7xl font-bold leading-tight">
                Feedback &
                <span class="block text-transparent bg-clip-text bg-gradient-to-r from-[color:var(--portal-gold)] to-amber-300 mt-2">
                    Suggestions
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                    </svg>
                </div>
                
                <div class="prose prose-lg max-w-none">
                    <h2 class="text-3xl md:text-4xl font-bold text-[color:var(--portal-navy)] mb-6">Feedback & Suggestions</h2>
                    <p class="text-lg text-slate-700 leading-relaxed mb-6">
                        The Feedback & Suggestions section allows students and staff to share their experiences, report issues, and suggest improvements for the University Academic Portal.
                    </p>
                    <p class="text-lg text-slate-700 leading-relaxed">
                        User feedback helps the university continuously improve system functionality, usability, and service quality.
                    </p>
                </div>
            </div>
        </div>

        {{-- Feedback Form --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 md:p-8 shadow-lg">
            @if (session('success'))
                <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">
                    <div class="font-semibold">Feedback submitted</div>
                    <div class="text-sm">{{ session('success') }}</div>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800">
                    <div class="font-semibold">Please fix the errors below</div>
                    <ul class="mt-2 list-disc pl-5 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('guest.feedback.store') }}" class="space-y-6" data-recaptcha-action="feedback">
                @csrf
                @if (config('recaptcha.site_key'))
                    <input type="hidden" name="recaptcha_token" value="">
                @endif
                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <label for="feedbackName" class="block text-sm font-semibold text-slate-700 mb-2">
                            Your Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="feedbackName"
                            name="name"
                            required
                            class="form-input w-full px-4 py-3 rounded-xl border border-slate-300 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[color:var(--portal-navy)] focus:border-transparent transition-all"
                            placeholder="John Doe"
                            value="{{ old('name') }}"
                        >
                    </div>
                    <div>
                        <label for="feedbackEmail" class="block text-sm font-semibold text-slate-700 mb-2">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="email"
                            id="feedbackEmail"
                            name="email"
                            required
                            class="form-input w-full px-4 py-3 rounded-xl border border-slate-300 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[color:var(--portal-navy)] focus:border-transparent transition-all"
                            placeholder="john.doe@example.com"
                            value="{{ old('email') }}"
                        >
                    </div>
                </div>
                
                <div>
                    <label for="feedbackType" class="block text-sm font-semibold text-slate-700 mb-2">
                        Feedback Type <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="feedbackType"
                        name="type"
                        required
                        class="form-input w-full px-4 py-3 rounded-xl border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-[color:var(--portal-navy)] focus:border-transparent transition-all cursor-pointer"
                    >
                        <option value="">Select feedback type</option>
                        <option value="suggestion" @selected(old('type') === 'suggestion')>Suggestion</option>
                        <option value="issue" @selected(old('type') === 'issue')>Report Issue</option>
                        <option value="compliment" @selected(old('type') === 'compliment')>Compliment</option>
                        <option value="other" @selected(old('type') === 'other')>Other</option>
                    </select>
                </div>
                
                <div>
                    <label for="feedbackMessage" class="block text-sm font-semibold text-slate-700 mb-2">
                        Your Feedback <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        id="feedbackMessage"
                        name="message"
                        rows="6"
                        required
                        class="form-input w-full px-4 py-3 rounded-xl border border-slate-300 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[color:var(--portal-navy)] focus:border-transparent transition-all resize-none"
                        placeholder="Please share your feedback, suggestions, or report any issues you've encountered..."
                    >{{ old('message') }}</textarea>
                </div>
                
                <button
                    type="submit"
                    class="w-full rounded-xl bg-gradient-to-r from-[color:var(--portal-navy)] to-slate-800 px-6 py-4 text-base font-semibold text-white shadow-lg hover:shadow-xl hover:scale-[1.02] transition-all duration-300"
                >
                    <span class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        Submit Feedback
                    </span>
                </button>
            </form>
        </div>

        {{-- Benefits of Feedback --}}
        <div class="grid gap-6 md:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 text-white mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Improve Functionality</h3>
                <p class="text-sm text-slate-600">Help us enhance portal features and capabilities.</p>
            </div>
            
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-green-500 to-green-600 text-white mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Better Experience</h3>
                <p class="text-sm text-slate-600">Contribute to a more user-friendly interface.</p>
            </div>
            
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 text-white mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Community Impact</h3>
                <p class="text-sm text-slate-600">Your input benefits all portal users.</p>
            </div>
        </div>
    </section>
</div>
@endsection
