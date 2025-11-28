<div class="card bg-base-100 shadow">
    <div class="card-body">
        <div class="flex items-center justify-between mb-4">
            <h2 class="card-title">
                {{ $isEditing ? 'Editar Avaliação' : 'Deixe sua Avaliação' }}
            </h2>

            @if($review && $review->isPending() && !$isEditing)
                <div class="flex gap-2">
                    <button
                        type="button"
                        class="btn btn-sm btn-ghost"
                        wire:click="edit"
                        title="Editar review"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </button>
                    <button
                        type="button"
                        class="btn btn-sm btn-ghost text-error"
                        wire:click="delete"
                        wire:confirm="Tem certeza que deseja remover esta review?"
                        title="Remover review"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            @endif
        </div>

        @if(session()->has('success'))
            <div class="alert alert-success mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session()->has('error'))
            <div class="alert alert-error mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if($review && $review->isRejected())
            {{-- Review Rejected --}}
            <div class="alert alert-warning mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div class="flex-1">
                    <h3 class="font-bold">Review Rejeitada</h3>
                    <div class="text-sm mt-2">
                        <p class="font-semibold mb-2">Sua avaliação:</p>
                        <div class="mb-3">
                            <div class="rating rating-lg mb-2">
                                @for($i = 5; $i >= 1; $i--)
                                    <input
                                        type="radio"
                                        class="mask mask-star-2 bg-orange-400"
                                        @if((6 - $i) <= $review->rating) checked @endif
                                        disabled
                                    />
                                @endfor
                            </div>
                            <p class="text-gray-700">{{ $review->comment }}</p>
                        </div>
                        @if($review->rejection_reason)
                            <div class="mt-3 p-3 bg-base-200 rounded">
                                <p class="font-semibold mb-1">Justificativa do administrador:</p>
                                <p class="text-sm">{{ $review->rejection_reason }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @elseif($review && $review->isPending() && !$isEditing)
            {{-- Review Pending --}}
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Sua avaliação:</p>
                    <div class="rating rating-lg mb-3">
                        @for($i = 5; $i >= 1; $i--)
                            <input
                                type="radio"
                                class="mask mask-star-2 bg-orange-400"
                                @if((6 - $i) <= $review->rating) checked @endif
                                disabled
                            />
                        @endfor
                    </div>
                    <div class="p-4 bg-base-200 rounded-lg">
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $review->comment }}</p>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Status: <span class="badge badge-warning">Pendente de aprovação</span></p>
                </div>
            </div>
        @else
            {{-- Review Form - Create or Edit --}}
            <form wire:submit.prevent="submit">
                <div class="form-control mb-6">
                    <label class="label">
                        <span class="label-text font-medium">Avaliação (1 a 5 estrelas)</span>
                    </label>
                    <div class="rating rating-lg gap-1">
                        @for($i = 5; $i >= 1; $i--)
                            <input
                                type="radio"
                                name="rating"
                                class="mask mask-star-2 bg-orange-400"
                                wire:click="$set('rating', {{ 6 - $i }})"
                                @if($rating == (6 - $i)) checked @endif
                            />
                        @endfor
                    </div>
                    @error('rating') <span class="text-error text-sm">{{ $message }}</span> @enderror
                </div>

                {{-- Comment --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Comentário</span>
                    </label>
                    <textarea
                        class="textarea textarea-bordered min-h-40 h-40 text-base mt-2"
                        placeholder="Escreva sua opinião sobre o livro..."
                        wire:model="comment"
                        rows="8"
                    ></textarea>
                    @error('comment') <span class="text-error text-sm mt-2">{{ $message }}</span> @enderror
                    <label class="label mt-2">
                        <span class="label-text-alt">{{ strlen($comment) }}/1000 caracteres</span>
                    </label>
                </div>

                <div class="card-actions justify-end mt-4">
                    @if($isEditing)
                        <button
                            type="button"
                            class="btn btn-ghost"
                            wire:click="cancelEdit"
                        >
                            Cancelar
                        </button>
                    @endif
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="submit">
                            {{ $isEditing ? 'Atualizar Avaliação' : 'Enviar Avaliação' }}
                        </span>
                        <span wire:loading wire:target="submit">
                            <span class="loading loading-spinner loading-sm"></span>
                            {{ $isEditing ? 'Atualizando...' : 'Enviando...' }}
                        </span>
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
