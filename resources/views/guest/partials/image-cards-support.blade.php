{{-- Support page: 3 visual feature cards — add your images under images/support/ --}}
<section class="space-y-6">
    <div class="text-center max-w-2xl mx-auto">
        <p class="text-xs font-semibold uppercase tracking-wide text-[color:var(--portal-navy)] mb-1">We're here to help</p>
        <h2 class="text-2xl md:text-3xl font-bold text-[color:var(--portal-navy)]">Support in One Place</h2>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="aspect-video w-full overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200">
                <img src="{{ asset('images/support/faq.png') }}" alt="FAQs" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="hidden h-full w-full items-center justify-center bg-gradient-to-br from-[color:var(--portal-navy)]/10 to-slate-200">
                    <span class="text-slate-400 text-sm font-medium">Add: images/support/faq.png</span>
                </div>
            </div>
            <div class="p-5 border-t border-slate-100">
                <h3 class="text-lg font-bold text-[color:var(--portal-navy)] mb-1">Guides & Self-Help</h3>
                <p class="text-sm text-slate-600">Step-by-step help and quick references for common portal tasks.</p>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="aspect-video w-full overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200">
                <img src="{{ asset('images/support/report.png') }}" alt="Report an Issue" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="hidden h-full w-full items-center justify-center bg-gradient-to-br from-[color:var(--portal-gold)]/10 to-slate-200">
                    <span class="text-slate-400 text-sm font-medium">Add: images/support/report_issue.jpg</span>
                </div>
            </div>
            <div class="p-5 border-t border-slate-100">
                <h3 class="text-lg font-bold text-[color:var(--portal-navy)] mb-1">Report an Issue</h3>
                <p class="text-sm text-slate-600">Tell us about technical problems so we can resolve them quickly.</p>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="aspect-video w-full overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200">
                <img src="{{ asset('images/support/contact.png') }}" alt="Contact Support" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="hidden h-full w-full items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200">
                    <span class="text-slate-400 text-sm font-medium">Add: images/support/contact_support.jpg</span>
                </div>
            </div>
            <div class="p-5 border-t border-slate-100">
                <h3 class="text-lg font-bold text-[color:var(--portal-navy)] mb-1">Contact Support</h3>
                <p class="text-sm text-slate-600">Reach our team by email, phone, or in person for direct help.</p>
            </div>
        </div>
    </div>
</section>
