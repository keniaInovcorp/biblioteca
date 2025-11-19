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
  </div>

</x-app-layout>
