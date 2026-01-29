{{-- Courses page: 4 visual feature cards — 2 per row --}}
<section class="space-y-6">

    <!-- Section heading -->
    <div class="text-center max-w-2xl mx-auto">
        <p class="text-xs font-semibold uppercase tracking-wide text-[color:var(--portal-navy)] mb-1">
            Programme highlights
        </p>
        <h2 class="text-2xl md:text-3xl font-bold text-[color:var(--portal-navy)]">
            Why Choose Our Courses
        </h2>
    </div>

    <!-- Grid: 2 + 2 -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

        <!-- Diverse Programs -->
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="aspect-video overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200">
                <img src="{{ asset('images/courses/diverse.png') }}" alt="Diverse Programs"
                     class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
            </div>
            <div class="p-4 border-t border-slate-100">
                <h3 class="font-bold text-[color:var(--portal-navy)] mb-1">Diverse Programs</h3>
                <p class="text-sm text-slate-600">
                    From sciences to arts — find the programme that fits your goals.
                </p>
            </div>
        </div>

        <!-- Expert Faculty -->
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="aspect-video overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200">
                <img src="{{ asset('images/courses/faculty.png') }}" alt="Expert Faculty"
                     class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
            </div>
            <div class="p-4 border-t border-slate-100">
                <h3 class="font-bold text-[color:var(--portal-navy)] mb-1">Expert Faculty</h3>
                <p class="text-sm text-slate-600">
                    Learn from experienced academics and industry practitioners.
                </p>
            </div>
        </div>

        <!-- Labs & Facilities -->
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="aspect-video overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200">
                <img src="{{ asset('images/courses/labs_facilities.png') }}" alt="Labs & Facilities"
                     class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
            </div>
            <div class="p-4 border-t border-slate-100">
                <h3 class="font-bold text-[color:var(--portal-navy)] mb-1">Labs & Facilities</h3>
                <p class="text-sm text-slate-600">
                    Hands-on learning in well-equipped labs and learning spaces.
                </p>
            </div>
        </div>

        <!-- Career Ready -->
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="aspect-video overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200">
                <img src="{{ asset('images/courses/career_ready.png') }}" alt="Career Ready"
                     class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
            </div>
            <div class="p-4 border-t border-slate-100">
                <h3 class="font-bold text-[color:var(--portal-navy)] mb-1">Career Ready</h3>
                <p class="text-sm text-slate-600">
                    Skills and placements that prepare you for the job market.
                </p>
            </div>
        </div>

    </div>

</section>
