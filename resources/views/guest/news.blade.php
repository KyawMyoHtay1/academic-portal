@extends('layouts.guest')

@section('title', 'News & Announcements')

@section('content')
<div class="container mx-auto px-4 py-10 space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-[color:var(--portal-navy)]">News & Announcements</h1>
        <a href="{{ route('guest.home') }}" class="text-[color:var(--portal-navy)] hover:underline text-sm font-semibold">Back to Home</a>
    </div>

    <div class="space-y-4">
        @forelse($announcements as $item)
            <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm hover:-translate-y-1 hover:shadow-md transition">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-xl font-semibold text-slate-900">{{ $item->title }}</h2>
                    <span class="text-xs text-slate-500">{{ $item->created_at->format('M d, Y') }}</span>
                </div>
                <p class="mt-2 text-sm text-slate-700">{{ $item->body }}</p>
            </article>
        @empty
            <p class="text-sm text-slate-600">No announcements available.</p>
        @endforelse
    </div>
</div>
@endsection

