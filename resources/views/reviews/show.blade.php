<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detalhes da Review
            </h2>
            <a href="{{ route('reviews.index') }}" class="btn btn-sm">Voltar</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <!-- Review Information -->
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h3 class="card-title">Informações da Review</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Cidadão</p>
                            <p class="font-semibold">{{ $review->user->name }}</p>
                            <p class="text-sm">{{ $review->user->email }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Livro</p>
                            <p class="font-semibold">{{ $review->book->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Avaliação</p>
                            <div class="rating rating-lg">
                                @for($i = 1; $i <= 5; $i++)
                                    <input
                                        type="radio"
                                        class="mask mask-star-2 bg-orange-400"
                                        @if($i <= $review->rating) checked @endif
                                        disabled
                                    />
                                @endfor
                            </div>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Status</p>
                            @if($review->status === 'pending')
                                <span class="badge badge-warning">Pendente</span>
                            @elseif($review->status === 'active')
                                <span class="badge badge-success">Ativo</span>
                            @else
                                <span class="badge badge-error">Rejeitado</span>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4">
                        <p class="text-sm text-gray-500">Comentário</p>
                        <p class="mt-2">{{ $review->comment }}</p>
                    </div>

                    @if($review->rejection_reason)
                        <div class="mt-4 alert alert-error">
                            <p class="font-semibold">Justificativa da Rejeição:</p>
                            <p>{{ $review->rejection_reason }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Moderation Actions -->
            @if($review->status === 'pending')
                <div class="card bg-base-100 shadow">
                    <div class="card-body">
                        <h3 class="card-title text-lg">Ações de Moderação</h3>

                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-2">
                            <!-- Rejection Form -->
                            <div class="md:col-span-1">
                                <form id="reject-form" action="{{ route('reviews.reject', $review) }}" method="POST">
                                    @csrf
                                    <div class="form-control">
                                        <textarea
                                            name="rejection_reason"
                                            class="textarea textarea-bordered"
                                            placeholder="Explique o motivo da rejeição..."
                                            rows="5"
                                            required
                                        ></textarea>
                                    </div>
                                    @error('rejection_reason')
                                        <div class="mt-2">
                                            <span class="text-error text-sm">{{ $message }}</span>
                                        </div>
                                    @enderror
                                </form>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-col gap-3 w-40">
                                <form id="approve-form" action="{{ route('reviews.approve', $review) }}" method="POST">
                                    @csrf
                                    <a href="#" onclick="event.preventDefault(); document.getElementById('approve-form').submit();" class="btn btn-success btn-sm w-full">
                                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Aprovar Review
                                    </a>
                                </form>

                                <a href="#" onclick="event.preventDefault(); document.getElementById('reject-form').submit();" class="btn btn-error btn-sm w-full">
                                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Rejeitar Review
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>