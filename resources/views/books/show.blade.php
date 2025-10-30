<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ver Livro</h2>
      <a href="{{ route('books.index') }}" class="btn">Voltar</a>
    </div>
  </x-slot>

  <div class="py-6">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
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
    </div>
  </div>
</x-app-layout>
