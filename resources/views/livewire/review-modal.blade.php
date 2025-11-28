<div>
    @if($showModal)
        <div class="modal modal-open" style="z-index: 9999;">
            <div class="modal-box max-w-md bg-base-100 shadow-2xl p-0">
                {{-- Header --}}
                <div class="bg-primary text-primary-content px-6 py-4 flex items-center justify-between">
                    <h3 class="font-bold text-lg flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        @if($currentReview && $currentReview->isPending() && !$isEditing)
                            A Sua Avaliação (Pendente)
                        @elseif($currentReview && $currentReview->isActive())
                            A Sua Avaliação (Publicada)
                        @elseif($currentReview && $currentReview->isRejected())
                            A Sua Avaliação (Rejeitada)
                        @elseif($isEditing)
                            Editar Avaliação
                        @else
                            Nova Avaliação
                        @endif
                    </h3>
                    <a href="#" wire:click.prevent="closeModal" class="btn btn-sm btn-circle btn-ghost text-primary-content hover:bg-primary-focus">✕</a>
                </div>

                <div class="px-6 py-5">
                    {{-- Success Message --}}
                    @if(session()->has('modal_success'))
                        <div class="alert alert-success mb-4 text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ session('modal_success') }}</span>
                        </div>
                    @endif

                    {{-- Error Message --}}
                    @if(session()->has('modal_error'))
                        <div class="alert alert-error mb-4 text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ session('modal_error') }}</span>
                        </div>
                    @endif

                    @if(!$canAccess)
                        <div class="alert alert-warning text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span>Precisa de ter requisitado e devolvido este livro para poder avaliar.</span>
                        </div>

                    @elseif($currentReview && $currentReview->isRejected())
                        {{-- Review was rejected - view only --}}
                        <div class="alert alert-warning mb-4 text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div>
                                <p class="font-semibold">Review Rejeitada</p>
                                @if($currentReview->rejection_reason)
                                    <p class="text-xs opacity-80 mt-1">{{ $currentReview->rejection_reason }}</p>
                                @endif
                            </div>
                        </div>

                        {{-- Show rejected review --}}
                        <div class="bg-base-200 rounded-lg p-4">
                            <div class="flex items-center gap-0.5 mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 {{ $i <= $currentReview->rating ? 'text-warning' : 'text-base-300' }}" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                @endfor
                            </div>
                            <p class="text-sm text-base-content/70">{{ $currentReview->comment }}</p>
                        </div>

                    @elseif($currentReview && $currentReview->isPending() && !$isEditing)
                        {{-- Pending review --}}
                        <div class="alert alert-info mb-4 text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Aguarda aprovação do administrador.</span>
                        </div>

                        {{-- Show pending review --}}
                        <div class="bg-base-200 rounded-lg p-4 mb-4">
                            <div class="flex items-center gap-0.5 mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 {{ $i <= $currentReview->rating ? 'text-warning' : 'text-base-300' }}" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                @endfor
                            </div>
                            <p class="text-sm text-base-content/70">{{ $currentReview->comment }}</p>
                        </div>

                        {{-- Edit/Delete  --}}
                        <div class="flex gap-3">
                            <a href="#" wire:click.prevent="startEditing" class="btn btn-outline btn-sm flex-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Editar
                            </a>
                            <a href="#"
                               x-data
                               @click.prevent="if(confirm('Tem certeza que deseja apagar?')) { $wire.call('deleteReview'); }"
                               class="btn btn-outline btn-error btn-sm flex-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Apagar
                            </a>
                        </div>

                    @elseif($isEditing)
                        {{-- Edit Form --}}
                        @include('livewire.partials.review-form')

                        <div class="flex gap-3 mt-4">
                            <a href="#" wire:click.prevent="cancelEditing" class="btn btn-ghost flex-1">
                                Cancelar
                            </a>
                            <a
                               href="#"
                               wire:click.prevent="submit"
                               wire:loading.class="opacity-50 pointer-events-none"
                               wire:target="submit"
                               id="save-review-button"
                               class="btn btn-primary flex-1">
                                <span wire:loading.remove wire:target="submit">Guardar</span>
                                <span wire:loading wire:target="submit" class="loading loading-spinner loading-sm"></span>
                            </a>
                        </div>

                    @elseif($currentReview && $currentReview->isActive())
                        {{-- Review was approved - view only --}}
                        <div class="alert alert-success mb-4 text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>A sua avaliação está publicada.</span>
                        </div>

                        {{-- Show published review --}}
                        <div class="bg-base-200 rounded-lg p-4">
                            <div class="flex items-center gap-0.5 mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 {{ $i <= $currentReview->rating ? 'text-warning' : 'text-base-300' }}" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                @endfor
                            </div>
                            <p class="text-sm text-base-content/70">{{ $currentReview->comment }}</p>
                        </div>

                    @elseif($canCreate)
                        {{-- New Review Form --}}
                        @include('livewire.partials.review-form')

                        <a href="#"
                           wire:click.prevent="submit"
                           wire:loading.class="opacity-50 pointer-events-none"
                           class="btn btn-primary w-full mt-4">
                            <span wire:loading.remove wire:target="submit">Enviar Avaliação</span>
                            <span wire:loading wire:target="submit" class="loading loading-spinner loading-sm"></span>
                        </a>

                        <p class="text-xs text-center text-base-content/50 mt-3">
                            Será revista por um administrador antes de ser publicada.
                        </p>

                    @else
                        {{-- Cannot create --}}
                        <div class="alert alert-info text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Não pode criar uma nova avaliação neste momento.</span>
                        </div>
                    @endif
                </div>
            </div>
            <div class="modal-backdrop bg-black/50" wire:click="closeModal"></div>
        </div>
    @endif
</div>
