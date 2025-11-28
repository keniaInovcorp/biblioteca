<div id="reviews">
    {{-- Reviews Header --}}
    <div class="flex items-center justify-between cursor-pointer select-none"
         wire:click="toggleExpanded">
        <h2 class="text-lg font-bold flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
            </svg>
            Avaliações
            <span class="badge badge-primary">{{ $reviewsCount }}</span>

            {{-- Average Rating --}}
            @if($reviewsCount > 0)
                <span class="text-sm font-normal text-base-content/60 ml-2">
                    ({{ $averageRating }}
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline text-warning" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>)
                </span>
            @endif
        </h2>

        <div class="btn btn-ghost btn-sm btn-circle">
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="h-5 w-5 transition-transform duration-200 {{ $expanded ? 'rotate-180' : '' }}"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
    </div>

    @if($expanded)
        <div class="mt-4" wire:transition>
            @if($reviewsCount > 0)
                <div class="flex items-center gap-4 mb-4 p-3 bg-base-200 rounded-lg">
                    <div class="text-center">
                        <p class="text-3xl font-bold text-primary">{{ $averageRating }}</p>
                        <div class="rating rating-sm">
                            @php $avgRatingRounded = round($averageRating); @endphp
                            @for($i = 1; $i <= 5; $i++)
                                <input type="radio" class="mask mask-star-2 bg-warning" {{ $i <= $avgRatingRounded ? 'checked' : '' }} disabled />
                            @endfor
                        </div>
                        <p class="text-xs text-base-content/60">{{ $reviewsCount }} {{ $reviewsCount === 1 ? 'avaliação' : 'avaliações' }}</p>
                    </div>
                </div>
            @endif

            {{-- Reviews List --}}
            <div class="space-y-3">
                @forelse($reviews as $review)
                    <div class="bg-base-200 rounded-lg p-4">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <div class="avatar placeholder">
                                    <div class="bg-primary text-primary-content rounded-full w-8">
                                        <span class="text-xs">{{ strtoupper(substr($review->user->name, 0, 1)) }}</span>
                                    </div>
                                </div>
                                <div>
                                    <p class="font-semibold text-sm">{{ $review->user->name }}</p>
                                    <p class="text-xs text-base-content/60">{{ $review->created_at->format('d/m/Y') }}</p>
                                </div>
                            </div>
                            <div class="rating rating-xs">
                                @for($i = 1; $i <= 5; $i++)
                                    <input type="radio" class="mask mask-star-2 bg-warning" {{ $i <= $review->rating ? 'checked' : '' }} disabled />
                                @endfor
                            </div>
                        </div>
                        <p class="text-sm text-base-content/80">{{ $review->comment }}</p>
                    </div>
                @empty
                    <div class="text-center py-6 text-base-content/50">
                        <p>Ainda não há avaliações para este livro.</p>
                        <p class="text-sm">Seja o primeiro a avaliar!</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($reviews->hasPages())
                <div class="mt-4 flex justify-center">
                    {{ $reviews->links() }}
                </div>
            @endif

        </div>
    @else
        <p class="text-sm text-base-content/60 mt-2">
            Clique para ver {{ $reviewsCount }} {{ $reviewsCount === 1 ? 'avaliação' : 'avaliações' }}
        </p>
    @endif
</div>
