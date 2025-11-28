<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Encomenda #{{ $order->order_number }}</h2>
            <a href="{{ route('orders.index') }}" class="btn btn-outline">Voltar</a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 py-8">
        @if(session('success'))
            <div class="alert alert-success mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="card bg-base-100 shadow-xl mb-6">
            <div class="card-body">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="card-title">Informações da Encomenda</h2>
                    @can('update', $order)
                        <div class="flex gap-2">
                            {{-- Mark as Shipped - Only if paid and processing --}}
                            @if($order->payment_status === 'paid' && $order->status === 'processing')
                                <form id="form-mark-as-shipped" method="POST" action="{{ route('orders.mark-as-shipped', $order) }}">
                                    @csrf
                                </form>
                                <a
                                    href="#"
                                    onclick="event.preventDefault(); if(confirm('Tem certeza que deseja marcar esta encomenda como enviada?')) { document.getElementById('form-mark-as-shipped').submit(); }"
                                    class="btn btn-success btn-sm"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 me-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Marcar como Enviada
                                </a>
                            @endif

                            {{-- Cancel - Only if payment pending and status pending --}}
                            @if($order->payment_status === 'pending' && $order->status === 'pending')
                                <form id="form-cancel" method="POST" action="{{ route('orders.cancel', $order) }}">
                                    @csrf
                                </form>
                                <a
                                    href="#"
                                    onclick="event.preventDefault(); if(confirm('Tem certeza que deseja cancelar esta encomenda?')) { document.getElementById('form-cancel').submit(); }"
                                    class="btn btn-error btn-sm"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 me-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Cancelar
                                </a>
                            @endif
                        </div>
                    @endcan
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p><strong>Número:</strong> {{ $order->order_number }}</p>
                        <p><strong>Status:</strong>
                            @php
                                $statusBadgeClass = match($order->status) {
                                    'processing' => 'badge-info',
                                    'shipped' => 'badge-info',
                                    'delivered' => 'badge-success',
                                    'cancelled' => 'badge-error',
                                    default => 'badge-warning',
                                };
                            @endphp
                            <span class="badge {{ $statusBadgeClass }}">
                                {{ match($order->status) {
                                    'processing' => 'Em Processamento',
                                    'shipped' => 'Enviada',
                                    'delivered' => 'Entregue',
                                    'cancelled' => 'Cancelada',
                                    default => ucfirst($order->status),
                                } }}
                            </span>
                        </p>
                        <p><strong>Pagamento:</strong>
                            <span class="badge badge-{{ $order->payment_status === 'paid' ? 'success' : ($order->payment_status === 'pending' ? 'warning' : 'error') }}">
                                {{ match($order->payment_status) {
                                    'paid' => 'Pago',
                                    'pending' => 'Pendente',
                                    'failed' => 'Falhou',
                                    default => ucfirst($order->payment_status),
                                } }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p><strong>Total:</strong> {{ number_format($order->total, 2, ',', '.') }} €</p>
                        <p><strong>Data:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card bg-base-100 shadow-xl mb-6">
            <div class="card-body">
                <h2 class="card-title mb-4">Endereço de Entrega</h2>
                <p>{{ $order->shipping_name }}</p>
                <p>{{ $order->shipping_email }}</p>
                @if($order->shipping_phone)
                    <p>{{ $order->shipping_phone }}</p>
                @endif
                <p>{{ $order->shipping_address_line_1 }}</p>
                @if($order->shipping_address_line_2)
                    <p>{{ $order->shipping_address_line_2 }}</p>
                @endif
                <p>{{ $order->shipping_postal_code }} {{ $order->shipping_city }}</p>
                <p>{{ $order->shipping_country }}</p>
            </div>
        </div>

        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title mb-4">Itens</h2>
                <div class="overflow-x-auto">
                    <table class="table table-zebra table-sm w-full">
                        <thead>
                            <tr>
                                <th>Livro</th>
                                <th>Quantidade</th>
                                <th>Preço Unitário</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td>{{ $item->book_name }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->price, 2, ',', '.') }} €</td>
                                    <td>{{ number_format($item->subtotal, 2, ',', '.') }} €</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-right font-bold">Total:</td>
                                <td class="font-bold">{{ number_format($order->total, 2, ',', '.') }} €</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
