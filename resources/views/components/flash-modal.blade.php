@if(session('success') || session('error'))
<div x-data="{ show: true }" 
     x-show="show" 
     style="display: none;"
     class="fixed inset-0 z-50 flex items-center justify-center">
    
    <!-- Backdrop Blur -->
    <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" 
         @click="show = false"
         x-show="show"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"></div>

    <!-- Modal Content -->
    <div class="relative z-10 w-full max-w-sm transform overflow-hidden rounded-2xl bg-white/95 dark:bg-[#1E293B]/95 backdrop-blur-xl p-6 text-left align-middle shadow-2xl transition-all border border-white/20 dark:border-[#F8FAFC]/10 m-4"
         x-show="show"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
        
        <div class="flex items-center justify-center mb-4">
            @if(session('success'))
            <div class="mx-auto flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/50">
                <svg class="h-8 w-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            @else
            <div class="mx-auto flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/50">
                <svg class="h-8 w-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
            @endif
        </div>
        
        <div class="mt-3 text-center sm:mt-5">
            <h3 class="text-xl font-bold leading-6 text-slate-900 dark:text-white" id="modal-title">
                {{ session('success') ? 'Berhasil!' : 'Gagal!' }}
            </h3>
            <div class="mt-2">
                <p class="text-sm text-slate-500 dark:text-slate-300 font-medium">
                    {{ session('success') ?? session('error') }}
                </p>
            </div>
        </div>
        
        <div class="mt-6 sm:mt-8">
            <button type="button" 
                    class="inline-flex w-full justify-center rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/30 hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 dark:focus:ring-offset-[#0F172A] transition-all" 
                    @click="show = false">
                Tutup
            </button>
        </div>
    </div>
</div>
@endif
