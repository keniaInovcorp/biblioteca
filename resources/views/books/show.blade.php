@php
    use Illuminate\Support\Str;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ver Livro</h2>
            <a href="{{ route('books.index') }}" class="btn btn-ghost btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Voltar
            </a>
        </div>
    </x-slot>

    <div class="container mx-auto px-4 py-8">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                @php
                    $reviewsCount = $book->activeReviews()->count();
                    $averageRating = round($book->activeReviews()->avg('rating') ?? 0, 1);
                @endphp

                <div class="flex flex-row gap-6 items-start">

                    <div style="width: 400px; min-width: 400px; max-width: 400px;" class="flex-shrink-0">
                        @if($book->cover_image_url)
                            <img
                                src="{{ $book->cover_image_url }}"
                                alt="{{ $book->name }}"
                                style="width: 400px; max-width: 400px;"
                                class="rounded shadow-md"
                            />
                        @else
                            <div style="width: 400px; height: 600px;" class="bg-base-300 rounded flex items-center justify-center shadow-md">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                        @endif

                        <div class="mt-3 text-center">
                            <a href="#reviews"
                               onclick="document.querySelector('[wire\\:click=\'toggleExpanded\']')?.click()"
                               class="text-xs font-bold uppercase tracking-wider text-base-content/60 mb-1 hover:text-primary cursor-pointer block">
                                Avaliações
                            </a>
                            <a href="#reviews"
                               onclick="document.querySelector('[wire\\:click=\'toggleExpanded\']')?.click()"
                               class="flex items-center justify-center gap-0.5 hover:opacity-80 cursor-pointer">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 {{ $i <= round($averageRating) ? 'text-warning' : 'text-base-300' }}" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                @endfor
                                <span class="text-xs text-base-content/60 ml-1">({{ $reviewsCount }})</span>
                            </a>
                            @auth
                                <button
                                    onclick="Livewire.dispatch('openReviewModal')"
                                    class="btn btn-outline btn-xs w-full mt-2">
                                    Adicionar Review
                                </button>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-outline btn-xs w-full mt-2">
                                    Adicionar Review
                                </a>
                            @endauth
                        </div>
                    </div>

                    <div class="flex-1">
                        <div class="flex justify-between items-start gap-6">
                            <div class="flex-1">
                                <h1 class="text-2xl lg:text-3xl font-bold text-base-content">{{ $book->name }}</h1>

                                @if($book->authors->count() > 0)
                                    <p class="text-primary mt-1">
                                        de <span class="font-medium">{{ $book->authors->pluck('name')->join(', ') }}</span>
                                    </p>
                                @endif


                                <div class="mt-4 space-y-1 text-sm text-base-content/80">
                                    <p><span class="font-medium">Editor:</span> {{ $book->publisher->name }}</p>
                                    <p><span class="font-medium">ISBN:</span> {{ $book->isbn }}</p>
                                    <p><span class="font-medium">Adicionado:</span> {{ $book->created_at->translatedFormat('F \d\e Y') }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 flex-shrink-0" style="margin-top: 20px; margin-right: 80px;">
                                @if($book->price)
                                    <p class="text-2xl font-bold text-primary whitespace-nowrap">
                                        {{ number_format($book->price, 2, ',', '.') }}€
                                    </p>
                                @endif

                                @can('viewAny', App\Models\Cart::class)
                                    <livewire:add-to-cart-button :book="$book" :inline="true" />
                                @endcan
                            </div>
                        </div>

                        @if($book->bibliography)
                            <div style="margin-top: 80px; margin-right: 20px;">
                                <p class="font-bold text-sm uppercase tracking-wider mb-2">Bibliografia</p>
                                <p class="text-base-content/80 leading-relaxed text-sm">{{ $book->bibliography }}</p>
                            </div>
                        @endif

                    </div>
                </div>

                @if($relatedBooks->isNotEmpty())
                    <div style="margin-top: 60px;">
                        <p class="font-bold text-sm uppercase tracking-wider mb-4">Livros Relacionados</p>
                        <div class="flex gap-4">
                            @foreach($relatedBooks->take(5) as $relatedBook)
                                <a href="{{ route('books.show', $relatedBook) }}" class="group" style="flex: 1 1 0; min-width: 0;">
                                    <div class="bg-base-200 hover:bg-base-300 transition-all hover:shadow-md rounded-lg overflow-hidden h-full">
                                        @if($relatedBook->cover_image_path)
                                            <img
                                                src="{{ $relatedBook->cover_image_url }}"
                                                alt="{{ $relatedBook->name }}"
                                                class="w-full aspect-[2/3] object-cover"
                                            />
                                        @else
                                            <div class="w-full aspect-[2/3] bg-base-300 flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="p-2">
                                            <h3 class="font-semibold text-xs line-clamp-2 group-hover:text-primary transition-colors">
                                                {{ Str::limit($relatedBook->name, 30) }}
                                            </h3>
                                            @if($relatedBook->price)
                                                <p class="text-sm font-bold text-primary mt-1">
                                                    {{ number_format($relatedBook->price, 2, ',', '.') }}€
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Reviews Section --}}
        <div class="card bg-base-100 shadow-xl mt-6" id="reviews">
            <div class="card-body">
                <livewire:book-reviews :book="$book" />
            </div>
        </div>

        {{-- Review Modal --}}
        @auth
            <livewire:review-modal :book="$book" />
        @endauth

        {{-- Request History - Admin --}}
        <livewire:book-submissions-table :book="$book" />
    </div>
</x-app-layout>
