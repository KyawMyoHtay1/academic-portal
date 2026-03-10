@extends('layouts.guest')

@section('title', $announcement->title . ' | News & Announcements')

@push('styles')
<style>
    .news-detail-prose p + p {
        margin-top: 1rem;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto max-w-7xl space-y-8 px-4 py-6">
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[color:var(--portal-navy)] via-slate-800 to-[color:var(--portal-navy)] px-6 py-10 text-white shadow-2xl md:px-10 md:py-14">
        <div class="absolute inset-0 opacity-20">
            <div class="absolute -right-8 top-0 h-64 w-64 rounded-full bg-[color:var(--portal-gold)] blur-3xl"></div>
            <div class="absolute -left-8 bottom-0 h-64 w-64 rounded-full bg-blue-500 blur-3xl"></div>
        </div>

        <div class="relative z-10 space-y-4">
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('guest.news') }}" class="inline-flex items-center gap-2 rounded-full border border-white/30 bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-slate-100 transition hover:bg-white/20">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to News
                </a>
                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wide {{ $announcement->priority === 'urgent' ? 'bg-red-500/90 text-white' : ($announcement->priority === 'important' ? 'bg-amber-500/90 text-white' : 'bg-blue-500/90 text-white') }}">
                    {{ ucfirst($announcement->priority ?? 'info') }}
                </span>
                @if($announcement->pinned)
                    <span class="inline-flex items-center rounded-full bg-[color:var(--portal-gold)] px-3 py-1 text-xs font-bold uppercase tracking-wide text-[color:var(--portal-navy)]">
                        Pinned
                    </span>
                @endif
            </div>

            <h1 class="max-w-4xl text-3xl font-bold leading-tight md:text-5xl">{{ $announcement->title }}</h1>

            <p class="text-sm text-slate-200 md:text-base">
                Posted on {{ $announcement->created_at->format('F d, Y \a\t h:i A') }}
            </p>
        </div>
    </section>

    <div class="grid gap-8 lg:grid-cols-3">
        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-md ring-1 ring-slate-900/5 lg:col-span-2 md:p-8">
            <div class="news-detail-prose text-base leading-relaxed text-slate-700">
                {!! nl2br(e($announcement->body)) !!}
            </div>

            <div class="mt-8 border-t border-slate-200 pt-6">
                <a href="{{ route('guest.news') }}" class="inline-flex items-center gap-2 rounded-full bg-[color:var(--portal-navy)] px-5 py-2.5 text-sm font-semibold text-white shadow-md transition hover:bg-slate-800">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    All Announcements
                </a>
            </div>
        </article>

        <aside class="space-y-4">
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-md ring-1 ring-slate-900/5">
                <h2 class="text-lg font-bold text-[color:var(--portal-navy)]">Related Announcements</h2>
                <p class="mt-1 text-sm text-slate-600">More updates you may want to read next.</p>
            </div>

            @forelse($relatedAnnouncements as $item)
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                    <div class="flex items-center justify-between gap-3">
                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $item->priority === 'urgent' ? 'bg-red-100 text-red-700' : ($item->priority === 'important' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700') }}">
                            {{ ucfirst($item->priority ?? 'info') }}
                        </span>
                        <span class="text-xs text-slate-500">{{ $item->created_at->format('M d, Y') }}</span>
                    </div>
                    <h3 class="mt-3 text-base font-bold text-slate-900">{{ \Illuminate\Support\Str::limit($item->title, 64) }}</h3>
                    <p class="mt-2 text-sm text-slate-600">{{ \Illuminate\Support\Str::limit($item->body, 110) }}</p>
                    <a href="{{ route('guest.news.show', $item->id) }}" class="mt-3 inline-flex items-center gap-1 text-sm font-semibold text-[color:var(--portal-navy)] hover:text-slate-900">
                        Read More
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-5 text-sm text-slate-600">
                    No related announcements available.
                </div>
            @endforelse
        </aside>
    </div>
</div>
@endsection
