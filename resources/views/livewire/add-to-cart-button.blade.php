<div>
    @auth
        @if($book->price && Auth::user()->hasRole('citizen'))
            <div class="card bg-base-100 shadow mt-6">
                <div class="card-body">
                    @if($successMessage)
                        <div x-data="{ show: true }"
                             x-init="setTimeout(() => { show = false; $wire.set('successMessage', '') }, 3000)"
                             x-show="show"
                             class="alert alert-success mb-4">
                            <svg class="stroke-current flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>{{ $successMessage }}</span>
                        </div>
                    @endif

                    <div class="flex items-center gap-4">
                        <div class="flex-1">
                            <label class="label">
                                <span class="label-text font-semibold">Quantidade</span>
                            </label>
                            <input type="number"
                                   wire:model="quantity"
                                   min="1" max="10"
                                   class="input input-bordered w-24">
                        </div>
                        <div class="flex-1">
                            <label class="label">
                                <span class="label-text font-semibold">Preço</span>
                            </label>
                            <p class="text-2xl font-bold">{{ number_format($book->price, 2, ',', '.') }} €</p>
                        </div>
                        <div class="flex items-end">
                            <button wire:click="addToCart"
                                    class="btn btn-primary btn-lg"
                                    wire:loading.attr="disabled">
                                <span wire:loading.remove>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Adicionar ao Carrinho
                                </span>
                                <span wire:loading>
                                    <span class="loading loading-spinner loading-sm"></span>
                                    Adicionando...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endauth
</div>