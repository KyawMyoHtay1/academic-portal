{{-- About page: 3 visual feature cards — add your images under images/about/ --}}
<section class="space-y-6">
    <div class="text-center max-w-2xl mx-auto">
        <p class="text-xs font-semibold uppercase tracking-wide text-[color:var(--portal-navy)] mb-1">Our story</p>
        <h2 class="text-2xl md:text-3xl font-bold text-[color:var(--portal-navy)]">See Who We Are</h2>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="aspect-video w-full overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200">
                <img src="{{ asset('images/about/ourcampus.jpg') }}" alt="Our Campus" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="hidden h-full w-full items-center justify-center bg-gradient-to-br from-[color:var(--portal-navy)]/10 to-slate-200">
                    <span class="text-slate-400 text-sm font-medium">Add: images/about/our_campus.jpg</span>
                </div>
            </div>
            <div class="p-5 border-t border-slate-100">
                <h3 class="text-lg font-bold text-[color:var(--portal-navy)] mb-1">Our Campus</h3>
                <p class="text-sm text-slate-600">Spaces and places that define everyday life at the university.</p>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="aspect-video w-full overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200">
                <img src="{{ asset('images/about/vision.jpg') }}" alt="Leadership" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="hidden h-full w-full items-center justify-center bg-gradient-to-br from-[color:var(--portal-gold)]/10 to-slate-200">
                    <span class="text-slate-400 text-sm font-medium">Add: images/about/leadership.jpg</span>
                </div>
            </div>
            <div class="p-5 border-t border-slate-100">
                <h3 class="text-lg font-bold text-[color:var(--portal-navy)] mb-1">Leadership & Vision</h3>
                <p class="text-sm text-slate-600">The people and direction driving our mission and growth.</p>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="aspect-video w-full overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200">
                <img src="{{ asset('images/about/milestones.jpg') }}" alt="Milestones" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="hidden h-full w-full items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200">
                    <span class="text-slate-400 text-sm font-medium">Add: images/about/milestones.jpg</span>
                </div>
            </div>
            <div class="p-5 border-t border-slate-100">
                <h3 class="text-lg font-bold text-[color:var(--portal-navy)] mb-1">Milestones & Achievements</h3>
                <p class="text-sm text-slate-600">Key moments and accolades in our journey.</p>
            </div>
        </div>
    </div>
</section>
