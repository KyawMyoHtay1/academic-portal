<section class="space-y-6">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Image 1 --}}
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
            <div class="aspect-video w-full overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200">
                <img 
                    src="{{ asset('images/home/modern_learning_1.png') }}"
                    alt="University Image 1"
                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                >

                {{-- Placeholder if image not found --}}
                <div class="hidden h-full w-full items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16
                                     m-2-2l1.586-1.586a2 2 0 012.828 0L20 14
                                     m-6-6h.01M6 20h12a2 2 0 002-2V6
                                     a2 2 0 00-2-2H6a2 2 0 00-2 2v12
                                     a2 2 0 002 2z"/>
                        </svg>
                        <p class="mt-2 text-xs font-semibold text-slate-500">Image 1</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Image 2 --}}
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
            <div class="aspect-video w-full overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200">
                <img 
                    src="{{ asset('images/home/modern_learning_2.png') }}"
                    alt="University Image 2"
                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                >

                {{-- Placeholder if image not found --}}
                <div class="hidden h-full w-full items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16
                                     m-2-2l1.586-1.586a2 2 0 012.828 0L20 14
                                     m-6-6h.01M6 20h12a2 2 0 002-2V6
                                     a2 2 0 00-2-2H6a2 2 0 00-2 2v12
                                     a2 2 0 002 2z"/>
                        </svg>
                        <p class="mt-2 text-xs font-semibold text-slate-500">Image 2</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

</section>
