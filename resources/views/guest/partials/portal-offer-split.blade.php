@php
    $sectionTitle = $title ?? 'What We Offer';
    $sectionLead = $lead ?? '';
    $sectionIntro = $intro ?? '';
    $sectionItems = $items ?? [];
    $sectionImage = $image ?? asset('images/fox_images/about-2.jpg');
    $sectionImageAlt = $imageAlt ?? 'University students';
@endphp

<section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-md ring-1 ring-slate-900/5">
    <div class="grid lg:grid-cols-5">
        <div class="bg-slate-100 p-8 md:p-10 lg:col-span-3">
            <div class="max-w-3xl space-y-4">
                <h2 class="text-3xl font-bold text-slate-900 md:text-4xl">{{ $sectionTitle }}</h2>
                @if (!empty($sectionLead))
                    <p class="text-base leading-relaxed text-slate-600">{{ $sectionLead }}</p>
                @endif
                @if (!empty($sectionIntro))
                    <p class="text-sm leading-relaxed text-slate-600 md:text-base">{{ $sectionIntro }}</p>
                @endif
            </div>

            <div class="mt-8 grid gap-6 md:grid-cols-2">
                @foreach($sectionItems as $item)
                    @php
                        $icon = $item['icon'] ?? 'sparkles';
                    @endphp
                    <article class="flex items-start gap-4">
                        <div class="mt-1 flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-orange-500 text-white shadow-lg">
                            @switch($icon)
                                @case('shield')
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3l7 4v5c0 4.5-3 7.8-7 9-4-1.2-7-4.5-7-9V7l7-4z"/>
                                    </svg>
                                    @break
                                @case('calendar')
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    @break
                                @case('check-badge')
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                    @break
                                @case('office')
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1"/>
                                    </svg>
                                    @break
                                @case('sparkles')
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3l2.5 6L14 11.5 7.5 14 5 20l-2.5-6L-4 11.5 2.5 9 5 3zM17 4l1.3 3 3 1.3-3 1.3L17 13l-1.3-3-3-1.3 3-1.3L17 4z"/>
                                    </svg>
                                    @break
                                @case('sport')
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 4h4l2 3 2-3h4l-2 5 2 11h-4l-2-7-2 7H6L8 9 6 4z"/>
                                    </svg>
                                    @break
                                @case('announcement')
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6"/>
                                    </svg>
                                    @break
                                @case('pin')
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                    </svg>
                                    @break
                                @case('clock')
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    @break
                                @case('document')
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    @break
                                @case('filter')
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18M6 12h12M10 20h4"/>
                                    </svg>
                                    @break
                                @case('archive')
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-1 12H5L4 7m16 0H4m4-3h8a1 1 0 011 1v2H7V5a1 1 0 011-1z"/>
                                    </svg>
                                    @break
                                @default
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                            @endswitch
                        </div>
                        <div class="space-y-1">
                            <h3 class="text-2xl font-bold text-slate-900">{{ $item['title'] ?? '' }}</h3>
                            <p class="text-base leading-relaxed text-slate-600">{{ $item['description'] ?? '' }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>

        <div class="relative min-h-[24rem] lg:col-span-2">
            <img
                src="{{ $sectionImage }}"
                alt="{{ $sectionImageAlt }}"
                class="h-full w-full object-cover"
                loading="lazy"
            >
        </div>
    </div>
</section>
