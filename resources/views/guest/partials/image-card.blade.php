{{-- Image Card Section - Ready for your images --}}
{{-- Replace the image paths below with your actual image URLs --}}
{{-- Supports 1 or 2 images - comment out the second image if you only want one --}}

<section class="space-y-6">
    {{-- Single Image Card (use this if you only have one image) --}}
    <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
        <div class="aspect-video w-full overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200">
            {{-- Replace 'images/home/custom_image_1.jpg' with your actual image path --}}
            <img 
                src="{{ asset('images/home/custom_image_1.jpg') }}" 
                alt="University Image" 
                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
            >
            {{-- Placeholder shown if image not found --}}
            <div class="hidden h-full w-full items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200">
                <div class="text-center">
                    <svg class="mx-auto h-16 w-16 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="mt-2 text-sm font-semibold text-slate-500">Add your image here</p>
                    <p class="text-xs text-slate-400">Update the image path in the template</p>
                </div>
            </div>
        </div>
        {{-- Optional: Add caption/description below image --}}
        {{-- <div class="p-4">
            <p class="text-sm text-slate-600">Your image caption or description here</p>
        </div> --}}
    </div>

    {{-- Two Image Cards Side by Side (uncomment if you want 2 images) --}}
    {{-- <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
            <div class="aspect-video w-full overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200">
                <img 
                    src="{{ asset('images/home/custom_image_1.jpg') }}" 
                    alt="University Image 1" 
                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                >
                <div class="hidden h-full w-full items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="mt-2 text-xs font-semibold text-slate-500">Image 1</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
            <div class="aspect-video w-full overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200">
                <img 
                    src="{{ asset('images/home/custom_image_2.jpg') }}" 
                    alt="University Image 2" 
                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                >
                <div class="hidden h-full w-full items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="mt-2 text-xs font-semibold text-slate-500">Image 2</p>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
</section>
