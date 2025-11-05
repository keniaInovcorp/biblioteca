<div>
    @if($this->book)
        <div class="card bg-base-200 mb-4">
            <div class="card-body">
                <div class="flex gap-4 items-start">
                    <div class="flex-shrink-0">
                        @if($this->book->cover_image_url)
                            <img src="{{ $this->book->cover_image_url }}" alt="{{ $this->book->name }}" class="w-32 rounded" />
                        @else
                            <div class="w-32 h-48 bg-gray-200 rounded flex items-center justify-center text-gray-400">
                                Sem capa
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold">{{ $this->book->name }}</h3>
                        <div class="mt-2 space-y-1 text-sm">
                            <p><span class="font-semibold">ISBN:</span> {{ $this->book->isbn }}</p>
                            <p><span class="font-semibold">Editora:</span> {{ $this->book->publisher->name }}</p>
                            @if($this->book->authors->count() > 0)
                                <p><span class="font-semibold">Autores:</span> {{ $this->book->authors->pluck('name')->join(', ') }}</p>
                            @else
                                <p><span class="font-semibold">Autores:</span> N/A</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
