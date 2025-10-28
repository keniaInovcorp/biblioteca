<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ver Editora</h2>
      <a href="{{ route('publishers.index') }}" class="btn">Voltar</a>
    </div>
  </x-slot>

  <div class="py-6">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
        <div class="flex gap-4 items-center">
          @if($publisher->logo_url)
            <img src="{{ $publisher->logo_url }}" alt="logo" class="w-32 rounded" />
          @endif

          <div>
            <h3 class="text-2xl font-bold">{{ $publisher->name }}</h3>
            <p class="text-sm text-gray-500">Criado a {{ $publisher->created_at->format('Y-m-d H:i') }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
