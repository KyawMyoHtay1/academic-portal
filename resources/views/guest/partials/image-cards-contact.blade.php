{{-- Contact page: 2 visual feature cards. Add your images under images/contact/ or images/home/. --}}
<section class="space-y-6">
    <div class="text-center max-w-2xl mx-auto">
        <p class="text-xs font-semibold uppercase tracking-wide text-[color:var(--portal-navy)] mb-1">Get in touch</p>
        <h2 class="text-2xl md:text-3xl font-bold text-[color:var(--portal-navy)]">Visit & Connect</h2>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="aspect-video w-full overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200">
                <img src="{{ asset('images/contact/visit.png') }}" alt="Visit guidance" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="hidden h-full w-full items-center justify-center bg-gradient-to-br from-[color:var(--portal-navy)]/10 to-slate-200">
                    <span class="text-slate-400 text-sm font-medium">Add: images/contact/visit_us.jpg</span>
                </div>
            </div>
            <div class="p-5 border-t border-slate-100">
                <h3 class="text-lg font-bold text-[color:var(--portal-navy)] mb-1">Visit Guidance</h3>
                <p class="text-sm text-slate-600">Use official university channels for campus visits, office hours, and directions.</p>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="aspect-video w-full overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200">
                <img src="{{ asset('images/contact/contact.png') }}" alt="Get in touch" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="hidden h-full w-full items-center justify-center bg-gradient-to-br from-[color:var(--portal-gold)]/10 to-slate-200">
                    <span class="text-slate-400 text-sm font-medium">Add: images/contact/get_in_touch.jpg</span>
                </div>
            </div>
            <div class="p-5 border-t border-slate-100">
                <h3 class="text-lg font-bold text-[color:var(--portal-navy)] mb-1">Get in Touch</h3>
                <p class="text-sm text-slate-600">Use the form or help desk and we will route your request to the right team.</p>
            </div>
        </div>
    </div>
</section>
