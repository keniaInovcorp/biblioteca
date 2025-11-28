<div>
    @if($canAdd)
        {{-- Success Message --}}
        @if($successMessage)
            <div x-data="{ show: true }"
                 x-init="setTimeout(() => { show = false; $wire.set('successMessage', '') }, 3000)"
                 x-show="show"
                 x-transition
                 class="alert alert-success mb-3 text-sm py-2">
                <svg class="stroke-current flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ $successMessage }}</span>
            </div>
        @endif

        @if($errorMessage)
            <div x-data="{ show: true }"
                 x-init="setTimeout(() => { show = false; $wire.set('errorMessage', '') }, 3000)"
                 x-show="show"
                 x-transition
                 class="alert alert-error mb-3 text-sm py-2">
                <svg class="stroke-current flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ $errorMessage }}</span>
            </div>
        @endif

        <a href="#"
           wire:click.prevent="addToCart"
           wire:loading.class="opacity-70 pointer-events-none"
           wire:target="addToCart"
           class="btn btn-lg bg-orange-500 hover:bg-orange-600 border-0 text-white font-bold uppercase tracking-wider"
           style="padding: 16px 32px; font-size: 18px;">
            <span wire:loading.remove wire:target="addToCart" class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                COMPRAR
            </span>
            <span wire:loading wire:target="addToCart" class="flex items-center gap-2">
                <span class="loading loading-spinner loading-md"></span>
            </span>
        </a>
    @endif
</div>
