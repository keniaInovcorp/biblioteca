<div class="max-w-7xl mx-auto p-4 space-y-4">
    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <div class="flex items-center justify-between mb-4">
                <h2 class="card-title">Google Books</h2>
                <a href="#" class="btn btn-active btn-accent btn-sm gap-2" wire:click.prevent="toggleAdvanced">
                    @if($showAdvanced)
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Voltar
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                        Pesquisa Avançada
                    @endif
                </a>
            </div>
            
            @if(!$showAdvanced)
                <label class="input input-bordered input-lg flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 opacity-70">
                        <path fill-rule="evenodd" d="M10.5 3.75a6.75 6.75 0 1 0 4.16 12.06l3.77 3.77a.75.75 0 1 0 1.06-1.06l-3.77-3.77A6.75 6.75 0 0 0 10.5 3.75Zm-5.25 6.75a5.25 5.25 0 1 1 10.5 0 5.25 5.25 0 0 1-10.5 0Z" clip-rule="evenodd"/>
                    </svg>
                    <input type="text" class="grow text-base" placeholder="Pesquisar livros..." wire:model.live.debounce.500ms="search" />
                    @if($search !== '')
                        <button type="button" class="btn btn-sm btn-ghost" wire:click="$set('search','')">Limpar</button>
                    @endif
                </label>
            @else
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text font-medium">Título</span></label>
                        <input type="text" wire:model.live.debounce.500ms="advTitle" class="input input-bordered w-full" placeholder="Ex: Harry Potter" />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-medium">Autor</span></label>
                        <input type="text" wire:model.live.debounce.500ms="advAuthor" class="input input-bordered w-full" placeholder="Ex: J.K. Rowling" />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-medium">ISBN</span></label>
                        <input type="text" wire:model.live.debounce.500ms="advIsbn" class="input input-bordered w-full" placeholder="Ex: 9780132350884" />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-medium">Editora</span></label>
                        <input type="text" wire:model.live.debounce.500ms="advPublisher" class="input input-bordered w-full" placeholder="Ex: O'Reilly" />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-medium">Ano de Publicação</span></label>
                        <input type="number" wire:model.live.debounce.500ms="advYear" class="input input-bordered w-full" placeholder="Ex: 2008" min="1900" max="{{ date('Y') }}" />
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if(session()->has('success'))
        <div class="alert alert-success">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session()->has('error'))
        <div class="alert alert-error">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if(!$hasSearch)
        <div class="alert alert-info">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>Digite algo para pesquisar livros no Google Books.</span>
        </div>
    @else
        <div class="card bg-base-100 shadow">
            <div class="card-body pb-0">
                <div class="flex justify-end">
                    <a 
                        href="#"
                        wire:click.prevent="importAll" 
                        wire:loading.attr="disabled"
                        wire:target="importAll"
                        class="btn btn-active btn-accent btn-sm gap-2"
                        title="Importar todos os livros da pesquisa">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" wire:loading.remove wire:target="importAll">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span class="loading loading-spinner loading-xs" wire:loading wire:target="importAll"></span>
                        <span class="ml-1">Importar Todos</span>
                    </a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="w-32">Capa</th>
                            <th>
                                <button class="flex items-center gap-1 hover:text-primary" wire:click="sortBy('title')">
                                    Título
                                    @if($sortField === 'title')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            @if($sortDir === 'asc')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            @endif
                                        </svg>
                                    @endif
                                </button>
                            </th>
                            <th>
                                <button class="flex items-center gap-1 hover:text-primary" wire:click="sortBy('author')">
                                    Autor(es)
                                    @if($sortField === 'author')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            @if($sortDir === 'asc')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            @endif
                                        </svg>
                                    @endif
                                </button>
                            </th>
                            <th>
                                <button class="flex items-center gap-1 hover:text-primary" wire:click="sortBy('publisher')">
                                    Editora
                                    @if($sortField === 'publisher')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            @if($sortDir === 'asc')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            @endif
                                        </svg>
                                    @endif
                                </button>
                            </th>
                            <th>
                                <button class="flex items-center gap-1 hover:text-primary" wire:click="sortBy('year')">
                                    Ano
                                    @if($sortField === 'year')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            @if($sortDir === 'asc')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            @endif
                                        </svg>
                                    @endif
                                </button>
                            </th>
                            <th class="text-center">ISBN</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($books as $item)
                            @php
                                $info = $item['volumeInfo'] ?? [];
                                $volumeId = $item['id'] ?? null;
                                $title = $info['title'] ?? 'Sem título';
                                $authors = isset($info['authors']) ? implode(', ', $info['authors']) : '—';
                                $publisher = $info['publisher'] ?? '—';
                                $year = isset($info['publishedDate']) ? substr($info['publishedDate'], 0, 4) : '—';
                                $img = $info['imageLinks']['thumbnail'] ?? $info['imageLinks']['smallThumbnail'] ?? null;
                                $identifiers = $info['industryIdentifiers'] ?? [];
                                $isbn = collect($identifiers)->where('type', 'ISBN_13')->first()['identifier'] 
                                     ?? collect($identifiers)->where('type', 'ISBN_10')->first()['identifier'] 
                                     ?? '—';
                            @endphp
                            <tr class="hover">
                                <td>
                                    @if($img)
                                        <div class="avatar">
                                            <div class="w-20 rounded">
                                                <img src="{{ str_replace('http:', 'https:', $img) }}" alt="{{ $title }}" class="object-cover" />
                                            </div>
                                        </div>
                                    @else
                                        <div class="w-20 h-28 bg-base-300 rounded flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                            </svg>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="font-semibold">{{ $title }}</div>
                                    @if(isset($info['subtitle']))
                                        <div class="text-sm opacity-60">{{ $info['subtitle'] }}</div>
                                    @endif
                                </td>
                                <td class="max-w-xs"><span class="text-sm">{{ $authors }}</span></td>
                                <td class="max-w-xs"><span class="text-sm">{{ $publisher }}</span></td>
                                <td><span class="badge badge-ghost">{{ $year }}</span></td>
                                <td class="text-center"><span class="text-xs font-mono">{{ $isbn }}</span></td>
                                <td class="text-center">
                                    @if($volumeId)
                                        @php
                                            $isImported = in_array($isbn, $importedIsbns);
                                        @endphp
                                        
                                        @if($isImported)
                                            <span 
                                                class="btn btn-active btn-accent btn-sm btn-disabled gap-2"
                                                title="Livro já importado">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </span>
                                        @else
                                            <a 
                                                href="#"
                                                wire:click.prevent="importBook('{{ $volumeId }}')" 
                                                wire:loading.attr="disabled"
                                                wire:target="importBook('{{ $volumeId }}')"
                                                class="btn btn-active btn-accent btn-sm gap-2"
                                                title="Importar para biblioteca">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" wire:loading.remove wire:target="importBook('{{ $volumeId }}')">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                                <span class="loading loading-spinner loading-xs" wire:loading wire:target="importBook('{{ $volumeId }}')"></span>
                                            </a>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-8 text-gray-500">Nenhum resultado encontrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($books->hasPages())
            {{ $books->onEachSide(1)->links() }}
        @endif
    @endif
</div>
