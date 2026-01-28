{{-- Academic Services page: 4 visual feature cards — add your images under images/services/ --}}
<section class="space-y-6">
    <div class="text-center max-w-2xl mx-auto">
        <p class="text-xs font-semibold uppercase tracking-wide text-[color:var(--portal-navy)] mb-1">Portal features</p>
        <h2 class="text-2xl md:text-3xl font-bold text-[color:var(--portal-navy)]">Services at a Glance</h2>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="aspect-video w-full overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200">
                <img src="{{ asset('images/services/enrollment.webp') }}" alt="Registration" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="hidden h-full w-full items-center justify-center bg-gradient-to-br from-[color:var(--portal-navy)]/10 to-slate-200">
                    <span class="text-slate-400 text-xs font-medium text-center px-2">images/services/registration.jpg</span>
                </div>
            </div>
            <div class="p-4 border-t border-slate-100">
                <h3 class="font-bold text-[color:var(--portal-navy)] mb-1">Registration & Enrolment</h3>
                <p class="text-sm text-slate-600">Enrol in courses and manage your programme in one place.</p>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="aspect-video w-full overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200">
                <img src="{{ asset('images/services/records.webp') }}" alt="Grades & Records" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="hidden h-full w-full items-center justify-center bg-gradient-to-br from-[color:var(--portal-gold)]/10 to-slate-200">
                    <span class="text-slate-400 text-xs font-medium text-center px-2">images/services/grades_records.jpg</span>
                </div>
            </div>
            <div class="p-4 border-t border-slate-100">
                <h3 class="font-bold text-[color:var(--portal-navy)] mb-1">Grades & Records</h3>
                <p class="text-sm text-slate-600">View grades, transcripts, and your academic history.</p>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="aspect-video w-full overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200">
                <img src="{{ asset('images/services/fee.jpg') }}" alt="Fees" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="hidden h-full w-full items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200">
                    <span class="text-slate-400 text-xs font-medium text-center px-2">images/services/fees.jpg</span>
                </div>
            </div>
            <div class="p-4 border-t border-slate-100">
                <h3 class="font-bold text-[color:var(--portal-navy)] mb-1">Fees & Payments</h3>
                <p class="text-sm text-slate-600">Check dues and pay securely through the portal.</p>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="aspect-video w-full overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200">
                <img src="{{ asset('images/services/alert.jpg') }}" alt="Timetables" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="hidden h-full w-full items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200">
                    <span class="text-slate-400 text-xs font-medium text-center px-2">images/services/timetables.jpg</span>
                </div>
            </div>
            <div class="p-4 border-t border-slate-100">
                <h3 class="font-bold text-[color:var(--portal-navy)] mb-1">Timetables & Alerts</h3>
                <p class="text-sm text-slate-600">Your schedule and important notifications in one view.</p>
            </div>
        </div>
    </div>
</section>
