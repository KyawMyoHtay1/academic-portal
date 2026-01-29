{{-- News page: 5 visual feature cards — 3 on first row, 2 on second --}}
<section class="space-y-6">

    <!-- Section heading -->
    <div class="text-center max-w-2xl mx-auto">
        <p class="text-xs font-semibold uppercase tracking-wide text-[color:var(--portal-navy)] mb-1">
            Stay informed
        </p>
        <h2 class="text-2xl md:text-3xl font-bold text-[color:var(--portal-navy)]">
            What’s Happening on Campus
        </h2>
    </div>

    <!-- Row 1 : 3 cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">

        <!-- Announcements -->
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="aspect-video overflow-hidden">
                <img src="{{ asset('images/news/announce2.png') }}" alt="Announcements"
                     class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
            </div>
            <div class="p-5 border-t">
                <h3 class="text-lg font-bold text-[color:var(--portal-navy)] mb-1">Announcements</h3>
                <p class="text-sm text-slate-600">Important updates, deadlines, and policy changes from the university.</p>
            </div>
        </div>

        <!-- Events -->
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="aspect-video overflow-hidden">
                <img src="{{ asset('images/news/events.jpg') }}" alt="Events"
                     class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
            </div>
            <div class="p-5 border-t">
                <h3 class="text-lg font-bold text-[color:var(--portal-navy)] mb-1">Events & Activities</h3>
                <p class="text-sm text-slate-600">Workshops, seminars, and social events you can join.</p>
            </div>
        </div>

        <!-- Research -->
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="aspect-video overflow-hidden">
                <img src="{{ asset('images/news/research.jpg') }}" alt="Research"
                     class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
            </div>
            <div class="p-5 border-t">
                <h3 class="text-lg font-bold text-[color:var(--portal-navy)] mb-1">Research & Innovation</h3>
                <p class="text-sm text-slate-600">Latest research news, grants, and achievements from our community.</p>
            </div>
        </div>

    </div>

    <!-- Row 2 : 2 cards (centered) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 max-w-4xl mx-auto">

        <!-- Opportunities -->
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="aspect-video overflow-hidden">
                <img src="{{ asset('images/news/opportunities.jpg') }}" alt="Student Opportunities"
                     class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
            </div>
            <div class="p-5 border-t">
                <h3 class="text-lg font-bold text-[color:var(--portal-navy)] mb-1">Student Opportunities</h3>
                <p class="text-sm text-slate-600">Scholarships, internships, and leadership calls you can apply for.</p>
            </div>
        </div>

        <!-- Community -->
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="aspect-video overflow-hidden">
                <img src="{{ asset('images/news/community.jpg') }}" alt="Community Stories"
                     class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
            </div>
            <div class="p-5 border-t">
                <h3 class="text-lg font-bold text-[color:var(--portal-navy)] mb-1">Community Stories</h3>
                <p class="text-sm text-slate-600">Highlights from clubs, societies, alumni, and partners.</p>
            </div>
        </div>

    </div>

</section>
