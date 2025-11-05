<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nova Requisição</h2>
            <a href="{{ route('submissions.index') }}" class="btn btn-outline">Voltar</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if (session('error'))
                <div class="alert alert-error mb-4" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <form method="POST" action="{{ route('submissions.store') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="label">
                            <span class="label-text">Livro <span class="text-error">*</span></span>
                        </label>
                        <select name="book_id" id="book_id" class="select select-bordered w-full" required 
                                x-on:change="$dispatch('book-selected', { bookId: $event.target.value })">
                            <option value="">Selecione um livro...</option>
                            @foreach(\App\Models\Book::whereDoesntHave('activeSubmissions')->with('publisher', 'authors')->get() as $book)
                                <option value="{{ $book->id }}" {{ old('book_id') == $book->id ? 'selected' : '' }}>
                                    {{ $book->name }} - {{ $book->isbn }} ({{ $book->publisher->name }})
                                </option>
                            @endforeach
                        </select>
                        @error('book_id') 
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p> 
                        @enderror
                    </div>

                    <livewire:book-preview wire:key="book-preview" />

                    <div class="mb-4 p-4 bg-base-200 rounded-lg">
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span class="font-semibold">Data de Início:</span>
                                <span class="text-gray-700">{{ \Carbon\Carbon::now()->locale('pt')->translatedFormat('d \d\e F \d\e Y') }}</span>
                                <span class="text-sm text-gray-500">(hoje)</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span class="font-semibold">Data de Fim Prevista:</span>
                                <span class="text-gray-700">{{ \Carbon\Carbon::now()->addDays(5)->locale('pt')->translatedFormat('d \d\e F \d\e Y') }}</span>
                                <span class="text-sm text-gray-500">(5 dias após a requisição)</span>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Após criar a requisição, você receberá um email de confirmação. Um administrador precisará confirmar a recepção do livro.</span>
                    </div>

                    <div class="flex gap-2">
                        <button class="btn btn-lg bg-green-500 hover:bg-green-600 border-green-500 px-6 py-3" type="submit" style="color: white !important;">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="white" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Requisitar
                        </button>

                        <a href="{{ route('submissions.index') }}" class="btn btn-outline">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-app-layout>