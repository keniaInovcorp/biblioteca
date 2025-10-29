<div>
    <label class="label">
        <span class="label-text">Autores <span class="text-sm opacity-60">(pode selecionar vários)</span></span>
    </label>

    <!-- Dropdown com Busca -->
    <div class="dropdown w-full">
        <div class="relative">
            <input 
                type="text" 
                wire:model.live.debounce.300ms="search"
                placeholder="Pesquisar e selecionar autores..." 
                class="input input-bordered w-full"
                autocomplete="off"
                onclick="@this.call('openDropdown')"
                onfocus="@this.call('openDropdown')"
                id="author-input-{{ $this->getId() }}"
            />
            <div 
                id="author-dropdown-{{ $this->getId() }}"
                class="dropdown-content z-[1] menu p-2 shadow-lg bg-base-100 rounded-box w-full max-h-64 overflow-y-auto border border-base-300 mt-1" 
                style="display: {{ $showDropdown ? 'block' : 'none' }};"
                wire:click.stop
            >
                @forelse($availableAuthors as $author)
                    <button 
                        type="button"
                        class="btn btn-ghost btn-sm justify-start w-full"
                        wire:click="addAuthor({{ $author->id }})"
                    >
                        {{ $author->name }}
                    </button>
                @empty
                    <div class="text-sm text-gray-500 p-2">Nenhum autor encontrado</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Autores Selecionados -->
    <div class="flex flex-wrap gap-2 mt-3 min-h-[2rem]">
        @foreach($selectedAuthorsData as $author)
            <div class="badge badge-primary gap-2">
                {{ $author->name }}
                <button 
                    type="button" 
                    class="btn btn-ghost btn-xs btn-circle" 
                    wire:click="removeAuthor({{ $author->id }})"
                >
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        @endforeach
    </div>

    <!-- Inputs Hidden para o Form -->
    @foreach($selectedAuthorsData as $author)
        <input type="hidden" name="authors[]" value="{{ $author->id }}" />
    @endforeach

    @error('authors') 
        <p class="text-sm text-red-500 mt-1">{{ $message }}</p> 
    @enderror
    @error('authors.*') 
        <p class="text-sm text-red-500 mt-1">{{ $message }}</p> 
    @enderror
</div>

