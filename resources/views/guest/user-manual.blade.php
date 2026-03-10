@extends('layouts.guest')

@section('title', 'User Manual')

@section('content')
<div class="container mx-auto max-w-7xl space-y-10 px-4 py-8">
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[color:var(--portal-navy)] via-slate-800 to-[color:var(--portal-navy)] px-6 py-12 text-white shadow-2xl ring-2 ring-white/10 md:px-10 md:py-16">
        <div class="absolute -right-20 -top-20 h-72 w-72 rounded-full bg-[color:var(--portal-gold)]/20 blur-3xl"></div>
        <div class="relative z-10 max-w-3xl">
            <p class="inline-flex items-center rounded-full bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.14em] text-amber-200 ring-1 ring-white/20">
                Documentation
            </p>
            <h1 class="mt-4 text-4xl font-bold md:text-5xl">University Academic Portal User Manual</h1>
            <p class="mt-4 text-sm leading-relaxed text-slate-200 md:text-base">
                View the complete user manual directly on this page. You can also open it in a new tab or download it as a PDF for offline use.
            </p>
            <div class="mt-6 flex flex-wrap items-center gap-3">
                <a href="{{ route('guest.user-manual.view') }}" target="_blank" rel="noopener" class="inline-flex items-center rounded-full bg-white px-6 py-3 text-sm font-semibold text-[color:var(--portal-navy)] hover:bg-slate-100">
                    Open In New Tab
                </a>
                <a href="{{ route('guest.user-manual.download') }}" class="inline-flex items-center rounded-full border border-white/40 bg-white/10 px-6 py-3 text-sm font-semibold text-white hover:bg-white/20">
                    Download PDF
                </a>
            </div>
        </div>
    </section>

    @include('guest.partials.quick-links-strip')

    <section class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm ring-1 ring-slate-900/5 md:p-6">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-bold text-[color:var(--portal-navy)] md:text-2xl">PDF Preview</h2>
            <p class="text-xs text-slate-500 md:text-sm">
                If preview is blocked by browser settings, use “Open In New Tab” or “Download PDF”.
            </p>
        </div>
        <iframe
            src="{{ route('guest.user-manual.view') }}"
            title="University Academic Portal User Manual PDF"
            class="h-[70vh] w-full rounded-xl border border-slate-200"
        ></iframe>
    </section>
</div>
@endsection
