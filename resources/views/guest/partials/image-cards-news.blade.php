{{-- News page: 3 visual feature cards — add your images under images/news/ --}}
<section class="space-y-6">
    <div class="text-center max-w-2xl mx-auto">
        <p class="text-xs font-semibold uppercase tracking-wide text-[color:var(--portal-navy)] mb-1">Stay informed</p>
        <h2 class="text-2xl md:text-3xl font-bold text-[color:var(--portal-navy)]">What’s Happening on Campus</h2>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="aspect-video w-full overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200">
                <img src="{{ asset('images/news/announce2.png') }}" alt="Announcements" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="hidden h-full w-full items-center justify-center bg-gradient-to-br from-[color:var(--portal-navy)]/10 to-slate-200">
                    <span class="text-slate-400 text-sm font-medium">Add: images/news/announcements.jpg</span>
                </div>
            </div>
            <div class="p-5 border-t border-slate-100">
                <h3 class="text-lg font-bold text-[color:var(--portal-navy)] mb-1">Announcements</h3>
                <p class="text-sm text-slate-600">Important updates, deadlines, and policy changes from the university.</p>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="aspect-video w-full overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200">
                <img src="{{ asset('images/news/events.jpg') }}" alt="Events" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="hidden h-full w-full items-center justify-center bg-gradient-to-br from-[color:var(--portal-gold)]/10 to-slate-200">
                    <span class="text-slate-400 text-sm font-medium">Add: images/news/events.jpg</span>
                </div>
            </div>
            <div class="p-5 border-t border-slate-100">
                <h3 class="text-lg font-bold text-[color:var(--portal-navy)] mb-1">Events & Activities</h3>
                <p class="text-sm text-slate-600">Workshops, seminars, and social events you can join.</p>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="aspect-video w-full overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200">
                <img src="{{ asset('images/news/research.jpg') }}" alt="Research" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="hidden h-full w-full items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200">
                    <span class="text-slate-400 text-sm font-medium">Add: images/news/research.jpg</span>
                </div>
            </div>
            <div class="p-5 border-t border-slate-100">
                <h3 class="text-lg font-bold text-[color:var(--portal-navy)] mb-1">Research & Innovation</h3>
                <p class="text-sm text-slate-600">Latest research news, grants, and achievements from our community.</p>
            </div>
        </div>
    </div>
</section>
