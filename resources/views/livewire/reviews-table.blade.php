<div class="space-y-4">
    <!-- Success Message -->
    @if($successMessage)
        <div x-data="{ show: true }" x-init="setTimeout(() => { show = false; $wire.set('successMessage', '') }, 5000)" x-show="show" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="alert alert-success shadow-lg">
            <svg class="stroke-current flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>{{ $successMessage }}</span>
        </div>
    @endif

    <!-- Status Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <!-- Card: Pendentes -->
        <div class="card bg-base-100 shadow-lg">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-base-content/70">Reviews Pendentes</h3>
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

        <!-- Card: Ativas -->
        <div class="card bg-base-100 shadow-lg">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-base-content/70">Reviews Ativas</h3>
                        <p class="text-3xl font-bold text-success mt-2 text-center">{{ $stats['active'] }}</p>
                    </div>
                    <div class="flex items-center justify-center w-16 h-16 rounded-full bg-success/10">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-success">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card: Rejeitadas -->
        <div class="card bg-base-100 shadow-lg">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-base-content/70">Reviews Rejeitadas</h3>
                        <p class="text-3xl font-bold text-error mt-2 text-center">{{ $stats['rejected'] }}</p>
                    </div>
                    <div class="flex items-center justify-center w-16 h-16 rounded-full bg-error/10">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-error">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-0">
            <div class="px-3 py-3">
                <h2 class="text-lg font-semibold whitespace-nowrap">Reviews</h2>
            </div>
            <table class="table table-zebra table-sm w-full">
                <thead>
                    <tr>
                        <th>Cidadão</th>
                        <th>Livro</th>
                        <th>
                            <button class="link link-hover font-semibold cursor-pointer {{ $sortField === 'rating' ? 'underline' : '' }}" wire:click="sortBy('rating')">
                                Avaliação
                                @if($sortField === 'rating')
                                    <span class="ml-1">{{ $sortDir === 'asc' ? '▲' : '▼' }}</span>
                                @endif
                            </button>
                        </th>
                        <th>Status</th>
                        <th>
                            <button class="link link-hover font-semibold cursor-pointer {{ $sortField === 'created_at' ? 'underline' : '' }}" wire:click="sortBy('created_at')">
                                Data
                                @if($sortField === 'created_at')
                                    <span class="ml-1">{{ $sortDir === 'asc' ? '▲' : '▼' }}</span>
                                @endif
                            </button>
                        </th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $review)
                        <tr>
                            <td>{{ $review->user->name }}</td>
                            <td>{{ $review->book->name }}</td>
                            <td>
                                <div class="rating rating-sm">
                                    @for($i = 1; $i <= 5; $i++)
                                        <input
                                            type="radio"
                                            class="mask mask-star-2 bg-orange-400"
                                            @if($i <= $review->rating) checked @endif
                                            disabled
                                        />
                                    @endfor
                                </div>
                            </td>
                            <td>
                                @if($review->status === 'pending')
                                    <span class="badge badge-warning">Pendente</span>
                                @elseif($review->status === 'active')
                                    <span class="badge badge-success">Ativo</span>
                                @else
                                    <span class="badge badge-error">Rejeitado</span>
                                @endif
                            </td>
                            <td>{{ $review->created_at->format('d/m/Y H:i') }}</td>
                            <td class="text-center">
                                <a href="{{ route('reviews.show', $review) }}" class="btn btn-sm btn-primary">
                                    Ver Detalhes
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="py-10 text-center text-sm opacity-60">Nenhuma review encontrada.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($reviews->hasPages())
        {{ $reviews->links() }}
    @endif
</div>

