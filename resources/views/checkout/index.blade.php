<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Checkout</h2>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Steps with indicators and progress bar-->
        <div class="flex items-center justify-center mb-8">
            <!-- Step 1: Cart completed-->
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-full bg-success text-success-content flex items-center justify-center font-bold">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <span class="ml-2 font-medium text-success">Carrinho</span>
            </div>

            <!-- Line 1-2 completed -->
            <div class="w-16 sm:w-24 h-1 bg-success mx-2"></div>

            <!-- Step 2: Delivery-current -->
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-full bg-primary text-primary-content flex items-center justify-center font-bold ring-4 ring-primary/30">
                    2
                </div>
                <span class="ml-2 font-medium text-primary">Entrega</span>
            </div>

            <!--Line 2-3 pending-->
            <div class="w-16 sm:w-24 h-1 bg-base-300 mx-2"></div>

            <!-- Step 3: Payment pending-->
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-full bg-base-300 text-base-content/50 flex items-center justify-center font-bold">
                    3
                </div>
                <span class="ml-2 font-medium text-base-content/50">Pagamento</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Shipping Form -->
            <div class="lg:col-span-2">
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title mb-6">Dados de Entrega</h2>

                        <form action="{{ route('checkout.store') }}" method="POST">
                            @csrf

                            <!-- Name and email -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div class="form-control w-full">
                                    <label class="label" for="shipping_name">
                                        <span class="label-text font-semibold">Nome Completo *</span>
                                    </label>
                                    <input type="text"
                                           id="shipping_name"
                                           name="shipping_name"
                                           value="{{ old('shipping_name', auth()->user()->name) }}"
                                           class="input input-bordered w-full @error('shipping_name') input-error @enderror"
                                           placeholder="Maria Silva"
                                           required>
                                    @error('shipping_name')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>

                                <div class="form-control w-full">
                                    <label class="label" for="shipping_email">
                                        <span class="label-text font-semibold">Email *</span>
                                    </label>
                                    <input type="email"
                                           id="shipping_email"
                                           name="shipping_email"
                                           value="{{ old('shipping_email', auth()->user()->email) }}"
                                           class="input input-bordered w-full @error('shipping_email') input-error @enderror"
                                           placeholder="maria@exemplo.com"
                                           required>
                                    @error('shipping_email')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>
                            </div>

                            <!-- Phone -->
                            <div class="form-control w-full mb-4">
                                <label class="label" for="shipping_phone">
                                    <span class="label-text font-semibold">Telefone</span>
                                </label>
                                <input type="tel"
                                       id="shipping_phone"
                                       name="shipping_phone"
                                       value="{{ old('shipping_phone') }}"
                                       class="input input-bordered w-full @error('shipping_phone') input-error @enderror"
                                       placeholder="123 456 789">
                                @error('shipping_phone')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Address Line 1 -->
                            <div class="form-control w-full mb-4">
                                <label class="label" for="shipping_address_line_1">
                                    <span class="label-text font-semibold">Endereço *</span>
                                </label>
                                <input type="text"
                                       id="shipping_address_line_1"
                                       name="shipping_address_line_1"
                                       value="{{ old('shipping_address_line_1') }}"
                                       class="input input-bordered w-full @error('shipping_address_line_1') input-error @enderror"
                                       placeholder="Rua, número, andar"
                                       required>
                                @error('shipping_address_line_1')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Address Line 2 -->
                            <div class="form-control w-full mb-4">
                                <label class="label" for="shipping_address_line_2">
                                    <span class="label-text font-semibold">Complemento</span>
                                </label>
                                <input type="text"
                                       id="shipping_address_line_2"
                                       name="shipping_address_line_2"
                                       value="{{ old('shipping_address_line_2') }}"
                                       class="input input-bordered w-full @error('shipping_address_line_2') input-error @enderror"
                                       placeholder="Apartamento, bloco. (opcional)">
                                @error('shipping_address_line_2')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- City, Postal Code and Country -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                <div class="form-control w-full">
                                    <label class="label" for="shipping_city">
                                        <span class="label-text font-semibold">Cidade *</span>
                                    </label>
                                    <input type="text"
                                           id="shipping_city"
                                           name="shipping_city"
                                           value="{{ old('shipping_city') }}"
                                           class="input input-bordered w-full @error('shipping_city') input-error @enderror"
                                           placeholder="Lisboa"
                                           required>
                                    @error('shipping_city')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>

                                <div class="form-control w-full">
                                    <label class="label" for="shipping_postal_code">
                                        <span class="label-text font-semibold">Código Postal *</span>
                                    </label>
                                    <input type="text"
                                           id="shipping_postal_code"
                                           name="shipping_postal_code"
                                           value="{{ old('shipping_postal_code') }}"
                                           class="input input-bordered w-full @error('shipping_postal_code') input-error @enderror"
                                           placeholder="1000-000"
                                           required>
                                    @error('shipping_postal_code')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>

                                <div class="form-control w-full">
                                    <label class="label" for="shipping_country">
                                        <span class="label-text font-semibold">País *</span>
                                    </label>
                                    <input type="text"
                                           id="shipping_country"
                                           name="shipping_country"
                                           value="{{ old('shipping_country', 'Portugal') }}"
                                           class="input input-bordered w-full @error('shipping_country') input-error @enderror"
                                           required>
                                    @error('shipping_country')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="flex flex-col sm:flex-row gap-4 mt-6">
                                <a href="{{ route('cart.index') }}" class="btn btn-outline flex-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                    </svg>
                                    Voltar ao Carrinho
                                </a>
                                <a href="#"
                                   onclick="event.preventDefault(); this.closest('form').submit();"
                                   class="btn btn-primary flex-1">
                                    Continuar para Pagamento
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="card bg-base-100 shadow-xl sticky top-4">
                    <div class="card-body">
                        <h2 class="card-title mb-4">Resumo do Pedido</h2>

                        <div class="space-y-3 mb-4">
                            @foreach($cart->items as $item)
                                <div class="flex items-center text-sm gap-3">
                                    <span class="flex-1 truncate">{{ $item->book->name }}</span>
                                    <span class="text-base-content/60 whitespace-nowrap">x{{ $item->quantity }}</span>
                                    <span class="font-medium whitespace-nowrap">{{ number_format($item->subtotal, 2, ',', '.') }} €</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="divider my-2"></div>

                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span>Subtotal:</span>
                                <span>{{ number_format($cart->total, 2, ',', '.') }} €</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span>Envio:</span>
                                <span class="text-success font-medium">Grátis</span>
                            </div>
                        </div>

                        <div class="divider my-2"></div>

                        <div class="flex justify-between font-bold text-lg">
                            <span>Total:</span>
                            <span class="text-primary">{{ number_format($cart->total, 2, ',', '.') }} €</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
