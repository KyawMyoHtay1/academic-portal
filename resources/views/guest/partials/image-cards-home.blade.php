{{-- Home page: 3 visual feature cards with image + title + short text --}}
{{-- Replace image paths below with your own (e.g. images/home/campus_life.jpg) --}}
<section class="space-y-6">
    <div class="text-center max-w-2xl mx-auto">
        <p class="text-xs font-semibold uppercase tracking-wide text-[color:var(--portal-navy)] mb-1">Highlights</p>
        <h2 class="text-2xl md:text-3xl font-bold text-[color:var(--portal-navy)]">Explore Our Campus & Community</h2>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="aspect-video w-full overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200">
                <img src="{{ asset('images/home/campuslife.png') }}" alt="Campus Life" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="hidden h-full w-full items-center justify-center bg-gradient-to-br from-[color:var(--portal-navy)]/10 to-slate-200">
                    <span class="text-slate-400 text-sm font-medium">Add: images/home/campus_life.jpg</span>
                </div>
            </div>
            <div class="p-5 border-t border-slate-100">
                <h3 class="text-lg font-bold text-[color:var(--portal-navy)] mb-1">Campus Life</h3>
                <p class="text-sm text-slate-600">Vibrant student community, clubs, and events that make your university experience memorable.</p>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="aspect-video w-full overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200">
                <img src="{{ asset('images/home/learningexcellence.png') }}" alt="Learning Excellence" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="hidden h-full w-full items-center justify-center bg-gradient-to-br from-[color:var(--portal-gold)]/10 to-slate-200">
                    <span class="text-slate-400 text-sm font-medium">Add: images/home/learning_excellence.jpg</span>
                </div>
            </div>
            <div class="p-5 border-t border-slate-100">
                <h3 class="text-lg font-bold text-[color:var(--portal-navy)] mb-1">Learning Excellence</h3>
                <p class="text-sm text-slate-600">Modern classrooms, libraries, and digital resources designed to support your academic success.</p>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="aspect-video w-full overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200">
                <img src="{{ asset('images/home/community.png') }}" alt="Community" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="hidden h-full w-full items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200">
                    <span class="text-slate-400 text-sm font-medium">Add: images/home/community.jpg</span>
                </div>
            </div>
            <div class="p-5 border-t border-slate-100">
                <h3 class="text-lg font-bold text-[color:var(--portal-navy)] mb-1">Community & Support</h3>
                <p class="text-sm text-slate-600">Faculty, staff, and peers working together to help you reach your goals.</p>
            </div>
        </div>
    </div>
</section>
