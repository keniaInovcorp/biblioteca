<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pagamento</h2>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Steps with indicators and progress bar-->
        <div class="flex items-center justify-center mb-8">
            <!-- Step 1: Cart completed -->
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

            <!-- Step 2: Delivery completed -->
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-full bg-success text-success-content flex items-center justify-center font-bold">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <span class="ml-2 font-medium text-success">Entrega</span>
            </div>

             <!-- Line 2-3 completed -->
            <div class="w-16 sm:w-24 h-1 bg-success mx-2"></div>

            <!-- Step 3: Payment-current-->
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-full bg-primary text-primary-content flex items-center justify-center font-bold ring-4 ring-primary/30">
                    3
                </div>
                <span class="ml-2 font-medium text-primary">Pagamento</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title mb-4">Informações de Pagamento</h2>

                        <livewire:stripe-payment 
                            :order="$order" 
                            :client-secret="$paymentIntent->client_secret" 
                            :stripe-key="$stripeKey" 
                        />
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1">
                <div class="card bg-base-100 shadow-xl sticky top-4">
                    <div class="card-body">
                        <h2 class="card-title mb-4">Resumo do Pedido</h2>
                        <p class="text-sm mb-2"><strong>Número:</strong> {{ $order->order_number }}</p>

                        <div class="space-y-3 mb-4">
                            @foreach($order->items as $item)
                                <div class="flex items-center text-sm gap-3">
                                    <span class="flex-1 truncate">{{ $item->book_name }}</span>
                                    <span class="text-base-content/60 whitespace-nowrap">x{{ $item->quantity }}</span>
                                    <span class="font-medium whitespace-nowrap">{{ number_format($item->subtotal, 2, ',', '.') }} €</span>
                                </div>
                            @endforeach
                            <div class="divider my-2"></div>
                            <div class="flex justify-between font-bold text-lg">
                                <span>Total:</span>
                                <span class="text-primary">{{ number_format($order->total, 2, ',', '.') }} €</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
