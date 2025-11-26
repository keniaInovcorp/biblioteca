<div class="space-y-4">
    <!-- Success/Error Messages -->
    @if($successMessage)
        <div x-data="{ show: true }"
             x-init="setTimeout(() => { show = false; $wire.set('successMessage', '') }, 3000)"
             x-show="show"
             x-transition
             class="alert alert-success shadow-lg">
            <svg class="stroke-current flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
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
             class="alert alert-error shadow-lg">
            <svg class="stroke-current flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>{{ $errorMessage }}</span>
        </div>
    @endif

    @php
        $hasItems = $cart && $cart->items && $cart->items->count() > 0;
    @endphp

    @if($hasItems)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Cart Items -->
            <div class="lg:col-span-2">
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title mb-4">Itens no Carrinho</h2>

                        <div class="space-y-4">
                            @foreach($cart->items as $item)
                                <div class="flex gap-4 p-4 border border-base-300 rounded-lg">
                                    @if($item->book->cover_image_url)
                                        <img src="{{ $item->book->cover_image_url }}" alt="{{ $item->book->name }}"
                                             class="w-24 h-24 object-contain rounded">
                                    @else
                                        <div class="w-24 h-24 bg-base-300 rounded flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                            </svg>
                                        </div>
                                    @endif

                                    <div class="flex-1">
                                        <h3 class="font-semibold text-lg">{{ $item->book->name }}</h3>
                                        <p class="text-sm text-gray-500">{{ $item->book->publisher->name }}</p>
                                        <p class="text-sm font-semibold mt-2">{{ number_format($item->book->price, 2, ',', '.') }} €</p>
                                    </div>

                                    <div class="flex flex-col items-end gap-2">
                                        <div class="flex items-center gap-2">
                                            <input type="number"
                                                   value="{{ $item->quantity }}"
                                                   min="1" max="10"
                                                   class="input input-bordered input-sm w-20 text-center"
                                                   wire:change="updateQuantity({{ $item->book->id }}, $event.target.value)"
                                                   wire:loading.attr="disabled">
                                            <span wire:loading wire:target="updateQuantity" class="loading loading-spinner loading-xs"></span>
                                        </div>

                                        <a href="#"
                                           wire:click.prevent="removeItem({{ $item->book->id }})"
                                           wire:loading.attr="disabled"
                                           class="btn btn-sm btn-error">
                                            <span wire:loading.remove wire:target="removeItem">Remover</span>
                                            <span wire:loading wire:target="removeItem" class="loading loading-spinner loading-xs"></span>
                                        </a>

                                        <p class="text-sm font-semibold">
                                            Subtotal: {{ number_format($item->subtotal, 2, ',', '.') }} €
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="divider"></div>

                        <a href="#"
                           wire:click.prevent="clearCart"
                           wire:confirm="Tem certeza que deseja limpar o carrinho?"
                           wire:loading.attr="disabled"
                           class="btn btn-outline btn-error w-full">
                            <span wire:loading.remove wire:target="clearCart">Limpar Carrinho</span>
                            <span wire:loading wire:target="clearCart" class="loading loading-spinner loading-sm"></span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="card bg-base-100 shadow-xl sticky top-4">
                    <div class="card-body">
                        <h2 class="card-title mb-4">Resumo do Pedido</h2>

                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between">
                                <span>Subtotal:</span>
                                <span>{{ number_format($cart->total, 2, ',', '.') }} €</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Envio:</span>
                                <span>Grátis</span>
                            </div>
                            <div class="divider"></div>
                            <div class="flex justify-between font-bold text-lg">
                                <span>Total:</span>
                                <span>{{ number_format($cart->total, 2, ',', '.') }} €</span>
                            </div>
                        </div>

                        <a href="{{ route('checkout.index') }}" class="btn btn-primary w-full">
                            Finalizar Compra
                        </a>

                        <div class="alert alert-info shadow-lg mb-4">
                            <svg class="stroke-current flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm">Sistema de checkout em desenvolvimento.</span>
                        </div>

                        <a href="{{ route('books.index') }}" class="btn btn-outline w-full">
                            Continuar Comprando
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body text-center py-12">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 mx-auto opacity-30 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <h2 class="text-2xl font-bold mb-2">Seu carrinho está vazio</h2>
                <p class="text-gray-500 mb-6">Adicione livros ao carrinho para começar a comprar!</p>
                <a href="{{ route('books.index') }}" class="btn btn-primary">Ver Livros</a>
            </div>
        </div>
    @endif
</div>
