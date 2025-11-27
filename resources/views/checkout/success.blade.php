<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pedido Confirmado</h2>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto px-4 py-8">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body text-center">
                <svg class="w-24 h-24 mx-auto text-success mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>

                <h2 class="text-3xl font-bold mb-2">Pedido Confirmado!</h2>
                <p class="text-gray-500 mb-6">Obrigado pela sua compra.</p>

                <div class="bg-base-200 p-6 rounded-lg mb-6 text-left">
                    <p class="mb-2"><strong>Número do Pedido:</strong> {{ $order->order_number }}</p>
                    <p class="mb-2"><strong>Total:</strong> {{ number_format($order->total, 2, ',', '.') }} €</p>
                    <p class="mb-2"><strong>Status:</strong>
                        <span class="badge badge-success">{{ ucfirst($order->payment_status) }}</span>
                    </p>
                </div>

                <div class="flex gap-4 justify-center">
                    <a href="{{ route('books.index') }}" class="btn btn-primary">Continuar Comprando</a>
                    {{-- <a href="{{ route('orders.show', $order) }}" class="btn btn-outline">Ver Pedido</a> --}}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
