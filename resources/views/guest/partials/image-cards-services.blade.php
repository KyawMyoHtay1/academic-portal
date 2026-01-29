{{-- Academic Services page: 4 cards first row, 2 cards second row --}}
<section class="space-y-6">
    <div class="text-center max-w-2xl mx-auto">
        <p class="text-xs font-semibold uppercase tracking-wide text-[color:var(--portal-navy)] mb-1">
            Portal features
        </p>
        <h2 class="text-2xl md:text-3xl font-bold text-[color:var(--portal-navy)]">
            Services at a Glance
        </h2>
    </div>

    {{-- Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

        {{-- Card 1 --}}
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg hover:shadow-xl transition-all hover:-translate-y-1">
            <div class="aspect-video overflow-hidden">
                <img src="{{ asset('images/services/enrollment.webp') }}"
                     class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-500">
            </div>
            <div class="p-4">
                <h3 class="font-bold text-[color:var(--portal-navy)]">Registration & Enrolment</h3>
                <p class="text-sm text-slate-600">Enrol in courses and manage your programme.</p>
            </div>
        </div>

        {{-- Card 2 --}}
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg hover:shadow-xl transition-all hover:-translate-y-1">
            <div class="aspect-video overflow-hidden">
                <img src="{{ asset('images/services/records.webp') }}"
                     class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-500">
            </div>
            <div class="p-4">
                <h3 class="font-bold text-[color:var(--portal-navy)]">Grades & Records</h3>
                <p class="text-sm text-slate-600">View grades and academic history.</p>
            </div>
        </div>

        {{-- Card 3 --}}
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg hover:shadow-xl transition-all hover:-translate-y-1">
            <div class="aspect-video overflow-hidden">
                <img src="{{ asset('images/services/fee.jpg') }}"
                     class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-500">
            </div>
            <div class="p-4">
                <h3 class="font-bold text-[color:var(--portal-navy)]">Fees & Payments</h3>
                <p class="text-sm text-slate-600">Check dues and pay securely.</p>
            </div>
        </div>

        {{-- Card 4 --}}
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg hover:shadow-xl transition-all hover:-translate-y-1">
            <div class="aspect-video overflow-hidden">
                <img src="{{ asset('images/services/alert.jpg') }}"
                     class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-500">
            </div>
            <div class="p-4">
                <h3 class="font-bold text-[color:var(--portal-navy)]">Timetables & Alerts</h3>
                <p class="text-sm text-slate-600">Schedules and important notifications.</p>
            </div>
        </div>

        {{-- Card 5 (second row, spans 2 columns) --}}
        <div class="lg:col-span-2 group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg hover:shadow-xl transition-all hover:-translate-y-1">
            <div class="aspect-video overflow-hidden">
                <img src="{{ asset('images/services/library.webp') }}"
                     class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-500">
            </div>
            <div class="p-4">
                <h3 class="font-bold text-[color:var(--portal-navy)]">Library Services</h3>
                <p class="text-sm text-slate-600">Access digital and physical learning resources.</p>
            </div>
        </div>

        {{-- Card 6 (second row, spans 2 columns) --}}
        <div class="lg:col-span-2 group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg hover:shadow-xl transition-all hover:-translate-y-1">
            <div class="aspect-video overflow-hidden">
                <img src="{{ asset('images/services/support.jpg') }}"
                     class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-500">
            </div>
            <div class="p-4">
                <h3 class="font-bold text-[color:var(--portal-navy)]">Student Support</h3>
                <p class="text-sm text-slate-600">Academic help, guidance, and assistance.</p>
            </div>
        </div>

    </div>
</section>
