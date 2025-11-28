<div class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-2 mb-4">
        <!-- Card: Pendente -->
        <div class="card bg-base-100 shadow-lg">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-base-content/70">Pendente</h3>
                        <p class="text-3xl font-bold text-warning mt-2 text-center">{{ $stats['pending'] }}</p>
                    </div>
                    <div class="flex items-center justify-center w-16 h-16 rounded-full bg-warning/10">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-warning">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card: Cancelada -->
        <div class="card bg-base-100 shadow-lg">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-base-content/70">Cancelada</h3>
                        <p class="text-3xl font-bold text-error mt-2 text-center">{{ $stats['cancelled'] }}</p>
                    </div>
                    <div class="flex items-center justify-center w-16 h-16 rounded-full bg-error/10">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-error">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card: Pago -->
        <div class="card bg-base-100 shadow-lg">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-base-content/70">Pago</h3>
                        <p class="text-3xl font-bold text-success mt-2 text-center">{{ $stats['paid'] }}</p>
                    </div>
                    <div class="flex items-center justify-center w-16 h-16 rounded-full bg-success/10">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-success">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card: Enviado -->
        <div class="card bg-base-100 shadow-lg">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-base-content/70">Enviado</h3>
                        <p class="text-3xl font-bold text-info mt-2 text-center">{{ $stats['shipped'] }}</p>
                    </div>
                    <div class="flex items-center justify-center w-16 h-16 rounded-full bg-info/10">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-info">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow-xl overflow-x-auto">
        <div class="card-body p-0">
            <table class="table table-zebra table-sm">
                <thead>
                    <tr>
                        <th>Número</th>
                        @if(auth()->user()->can('create', \App\Models\Book::class))
                            <th>Cliente</th>
                        @endif
                        <th>Total</th>
                        <th>Status</th>
                        <th>Pagamento</th>
                        <th>Data</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td>{{ $order->order_number }}</td>
                            @if(auth()->user()->can('create', \App\Models\Book::class))
                                <td>{{ $order->user->name }}</td>
                            @endif
                            <td>{{ number_format($order->total, 2, ',', '.') }} €</td>
                            <td>
                                @php
                                    $statusBadgeClass = match($order->status) {
                                        'processing' => 'badge-info',
                                        'shipped' => 'badge-info',
                                        'delivered' => 'badge-success',
                                        'cancelled' => 'badge-error',
                                        default => 'badge-warning',
                                    };
                                    $statusLabel = match($order->status) {
                                        'pending' => 'Pendente',
                                        'processing' => 'Em Processamento',
                                        'shipped' => 'Enviada',
                                        'delivered' => 'Entregue',
                                        'cancelled' => 'Cancelada',
                                        default => ucfirst($order->status),
                                    };
                                @endphp
                                <span class="badge {{ $statusBadgeClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $order->payment_status === 'paid' ? 'success' : ($order->payment_status === 'pending' ? 'warning' : 'error') }}">
                                    {{ match($order->payment_status) {
                                        'paid' => 'Pago',
                                        'pending' => 'Pendente',
                                        'failed' => 'Falhou',
                                        default => ucfirst($order->payment_status),
                                    } }}
                                </span>
                            </td>
                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-primary">Ver</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->can('create', \App\Models\Book::class) ? '7' : '6' }}" class="text-center">
                                <div class="py-10 text-center text-sm opacity-60">Nenhuma encomenda encontrada.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($orders->hasPages())
        {{ $orders->links() }}
    @endif
</div>
