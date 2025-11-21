@php
    use Illuminate\Support\Str;
@endphp

<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ver Livro</h2>
      <a href="{{ route('books.index') }}" class="btn">Voltar</a>
    </div>
  </x-slot>

  <div class="container mx-auto px-4 py-8">
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
      <div class="flex gap-4 items-start">
        @if($book->cover_image_url)
          <img src="{{ $book->cover_image_url }}" alt="cover" class="w-32 rounded" />
        @endif

        <div class="flex-1">
          <h3 class="text-2xl font-bold">{{ $book->name }}</h3>
          <div class="mt-2 space-y-1 text-sm">
            <p><span class="font-semibold">ISBN:</span> {{ $book->isbn }}</p>
            <p><span class="font-semibold">Editora:</span> {{ $book->publisher->name }}</p>
            @if($book->authors->count() > 0)
              <p><span class="font-semibold">Autores:</span> {{ $book->authors->pluck('name')->join(', ') }}</p>
            @endif
            @if($book->price)
              <p><span class="font-semibold">Preço:</span> {{ number_format($book->price, 2, ',', '.') }} €</p>
            @endif
            @if($book->bibliography)
              <p class="mt-3"><span class="font-semibold">Bibliografia:</span></p>
              <p class="text-gray-600">{{ $book->bibliography }}</p>
            @endif
          </div>
          <p class="text-sm text-gray-500 mt-4">Criado a {{ $book->created_at->format('Y-m-d H:i') }}</p>
        </div>
      </div>
    </div>

    <!-- Request History - Admin -->
    <livewire:book-submissions-table :book="$book" />

  <!-- Reviews Section -->
  <div class="card bg-base-100 shadow mt-6">
    <div class="card-body">
        <h2 class="card-title">Avaliações ({{ $book->activeReviews->count() }})</h2>

        @forelse($book->activeReviews as $review)
            <div class="border-b border-base-300 pb-4 mb-4 last:border-0 last:mb-0">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <p class="font-semibold">{{ $review->user->name }}</p>
                        <p class="text-sm text-gray-500">{{ $review->created_at->format('d/m/Y') }}</p>
                    </div>
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
                </div>
                <p class="text-sm">{{ $review->comment }}</p>
            </div>
        @empty
            <p class="text-gray-500">Ainda não há avaliações para este livro.</p>
        @endforelse

        <!-- Formulário de Review  -->
        @auth
          @can('canReviewBook', $book->id)
                <div class="mt-6 pt-6 border-t border-base-300">
                    <livewire:review-form :book="$book" />
                </div>
            @endcan
        @endauth
    </div>
  </div>

    <!-- Livros Relacionados -->
    @if($relatedBooks->isNotEmpty())
      <div class="card bg-base-100 shadow mt-6">
        <div class="card-body p-2">
          <h2 class="card-title">Livros Relacionados</h2>

          <div class="flex flex-nowrap gap-2 overflow-x-auto">
            @foreach($relatedBooks as $relatedBook)
              <div class="card bg-base-200 shadow-sm rounded-md flex-shrink-0" style="width: calc(20% - 8px);">
                <div class="card-body p-2">
                  @if($relatedBook->cover_image_path)
                    <img
                      src="{{ $relatedBook->cover_image_url }}"
                      alt="{{ $relatedBook->name }}"
                      class="w-full aspect-square object-contain rounded mb-2"
                    />
                  @else
                    <div class="w-full aspect-square bg-base-300 rounded mb-2 flex items-center justify-center">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                      </svg>
                    </div>
                  @endif
                  <h3 class="font-semibold text-xs mb-1 line-clamp-2">{{ Str::limit($relatedBook->name, 30) }}</h3>
                  <p class="text-xs text-gray-500 mb-2 line-clamp-1">{{ $relatedBook->publisher->name }}</p>
                  <a
                    href="{{ route('books.show', $relatedBook) }}"
                    class="btn btn-xs btn-primary w-full"
                  >
                    Ver Detalhes
                 </a>
                </div>
               </div>
             @endforeach
           </div>
         </div>
       </div>
      @endif

  </div>
</x-app-layout>
