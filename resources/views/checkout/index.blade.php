<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Checkout</h2>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="steps mb-8">
            <div class="step step-primary">Carrinho</div>
            <div class="step step-primary">Entrega</div>
            <div class="step">Pagamento</div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Shipping Form -->
            <div class="lg:col-span-2">
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title mb-4">Dados de Entrega</h2>

                        <form action="{{ route('checkout.store') }}" method="POST">
                            @csrf

                            <div class="form-control mb-4">
                                <label class="label">
                                    <span class="label-text font-semibold">Nome Completo *</span>
                                </label>
                                <input type="text" name="shipping_name"
                                       value="{{ old('shipping_name', auth()->user()->name) }}"
                                       class="input input-bordered @error('shipping_name') input-error @enderror" required>
                                @error('shipping_name')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <div class="form-control mb-4">
                                <label class="label">
                                    <span class="label-text font-semibold">Email *</span>
                                </label>
                                <input type="email" name="shipping_email"
                                       value="{{ old('shipping_email', auth()->user()->email) }}"
                                       class="input input-bordered @error('shipping_email') input-error @enderror" required>
                                @error('shipping_email')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <div class="form-control mb-4">
                                <label class="label">
                                    <span class="label-text font-semibold">Telefone</span>
                                </label>
                                <input type="text" name="shipping_phone" value="{{ old('shipping_phone') }}"
                                       class="input input-bordered @error('shipping_phone') input-error @enderror">
                                @error('shipping_phone')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <div class="form-control mb-4">
                                <label class="label">
                                    <span class="label-text font-semibold">Endereço (Linha 1) *</span>
                                </label>
                                <input type="text" name="shipping_address_line_1" value="{{ old('shipping_address_line_1') }}"
                                       class="input input-bordered @error('shipping_address_line_1') input-error @enderror" required>
                                @error('shipping_address_line_1')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <div class="form-control mb-4">
                                <label class="label">
                                    <span class="label-text font-semibold">Endereço (Linha 2)</span>
                                </label>
                                <input type="text" name="shipping_address_line_2" value="{{ old('shipping_address_line_2') }}"
                                       class="input input-bordered @error('shipping_address_line_2') input-error @enderror">
                                @error('shipping_address_line_2')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-semibold">Cidade *</span>
                                    </label>
                                    <input type="text" name="shipping_city" value="{{ old('shipping_city') }}"
                                           class="input input-bordered @error('shipping_city') input-error @enderror" required>
                                    @error('shipping_city')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>

                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-semibold">Código Postal *</span>
                                    </label>
                                    <input type="text" name="shipping_postal_code" value="{{ old('shipping_postal_code') }}"
                                           class="input input-bordered @error('shipping_postal_code') input-error @enderror" required>
                                    @error('shipping_postal_code')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-control mb-4">
                                <label class="label">
                                    <span class="label-text font-semibold">País *</span>
                                </label>
                                <input type="text" name="shipping_country" value="{{ old('shipping_country', 'Portugal') }}"
                                       class="input input-bordered @error('shipping_country') input-error @enderror" required>
                                @error('shipping_country')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <div class="flex gap-4 mt-6">
                                <a href="{{ route('cart.index') }}" class="btn btn-outline flex-1">Voltar</a>
                                <button type="submit" class="btn btn-primary flex-1">Continuar para Pagamento</button>
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

                        <div class="space-y-2 mb-4">
                            @foreach($cart->items as $item)
                                <div class="flex justify-between text-sm">
                                    <span>{{ $item->book->name }} x{{ $item->quantity }}</span>
                                    <span>{{ number_format($item->subtotal, 2, ',', '.') }} €</span>
                                </div>
                            @endforeach
                            <div class="divider"></div>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>