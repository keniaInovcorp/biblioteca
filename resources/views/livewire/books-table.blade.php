<div class="space-y-4">
    <div class="card bg-base-100 shadow-xl overflow-x-auto">
        <div class="card-body p-0">
            <div class="px-3 py-3 flex items-center justify-between gap-2 whitespace-nowrap">
                <h2 class="text-lg font-semibold">Livros</h2>
                <div class="flex items-center gap-2 flex-1 justify-end">
                    <select class="select select-bordered select-xs min-w-0 w-auto flex-none shrink-0" wire:model.live="searchField">
                        <option value="all">Todos</option>
                        <option value="name">Nome</option>
                        <option value="publisher">Editora</option>
                        <option value="author">Autor</option>
                        <option value="price">Preço</option>
                    </select>
                    <label class="input input-bordered flex items-center gap-2 w-[32rem] md:w-[48rem] lg:w-[60rem]">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4 opacity-70"><path fill-rule="evenodd" d="M10.5 3.75a6.75 6.75 0 1 0 4.16 12.06l3.77 3.77a.75.75 0 1 0 1.06-1.06l-3.77-3.77A6.75 6.75 0 0 0 10.5 3.75Zm-5.25 6.75a5.25 5.25 0 1 1 10.5 0 5.25 5.25 0 0 1-10.5 0Z" clip-rule="evenodd"/></svg>
                        @php
                            $ph = 'Pesquisar';
                            if ($searchField === 'name') $ph = 'Pesquisar por nome';
                            elseif ($searchField === 'publisher') $ph = 'Pesquisar por editora';
                            elseif ($searchField === 'author') $ph = 'Pesquisar por autor';
                            elseif ($searchField === 'price') $ph = 'Preço (ex.: 12.5 ou 10-20)';
                            else $ph = 'Pesquisar por...';
                        @endphp
                        <input type="text" class="grow" placeholder="{{ $ph }}" wire:model.live.debounce.300ms="search" />
                        @if($search !== '')
                            <button type="button" class="btn btn-xs btn-ghost" wire:click="$set('search','')">Limpar</button>
                        @endif
                    </label>
                    <a href="{{ route('books.export', ['q' => $search, 'sfield' => $searchField, 'sort' => $sortField, 'dir' => $sortDir]) }}" class="btn btn-outline btn-xs" target="_blank">Exportar CSV</a>
                </div>
            </div>
            <table class="table table-zebra table-sm">
                <thead>
                    <tr>
                        <th class="w-16">Capa</th>
                        <th>
                            <button class="link link-hover font-semibold cursor-pointer" wire:click="sortBy('name')">
                                Nome
                                @if($sortField === 'name')
                                    <span class="ml-1">{{ $sortDir === 'asc' ? '▲' : '▼' }}</span>
                                @endif
                            </button>
                            <div class="text-xs opacity-60">ISBN</div>
                        </th>
                        <th>
                            <button class="link link-hover font-semibold cursor-pointer" wire:click="sortBy('publisher_name')">
                                Editora
                                @if($sortField === 'publisher_name')
                                    <span class="ml-1">{{ $sortDir === 'asc' ? '▲' : '▼' }}</span>
                                @endif
                            </button>
                        </th>
                        <th>
                            <button class="link link-hover font-semibold cursor-pointer" wire:click="sortBy('authors_min_name')">
                                Autores
                                @if($sortField === 'authors_min_name')
                                    <span class="ml-1">{{ $sortDir === 'asc' ? '▲' : '▼' }}</span>
                                @endif
                            </button>
                        </th>
                        <th>
                            <button class="link link-hover font-semibold cursor-pointer" wire:click="sortBy('price')">
                                Preço
                                @if($sortField === 'price')
                                    <span class="ml-1">{{ $sortDir === 'asc' ? '▲' : '▼' }}</span>
                                @endif
                            </button>
                        </th>
                        <th class="whitespace-nowrap">
                            <button class="link link-hover font-semibold cursor-pointer" wire:click="sortBy('created_at')">
                                Criado em
                                @if($sortField === 'created_at')
                                    <span class="ml-1">{{ $sortDir === 'asc' ? '▲' : '▼' }}</span>
                                @endif
                            </button>
                        </th>
                        <th class="w-24 text-center">
                            <div class="flex w-24 justify-center">Ações</div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($books as $book)
                        <tr>
                            <td>
                                @if($book->cover_image_url)
                                    <img class="size-10 rounded-box object-cover" src="{{ $book->cover_image_url }}" alt="{{ $book->name }}" />
                                @else
                                    <div class="avatar placeholder">
                                        <div class="bg-primary text-primary-content rounded-box size-10">
                                            <span class="text-sm font-bold">{{ substr($book->name, 0, 1) }}</span>
                                        </div>
                                    </div>
                                @endif
                            </td>
                            <td class="align-middle">
                                <div class="font-semibold">{{ $book->name }}</div>
                                <div class="text-xs opacity-60">ISBN: {{ $book->isbn }}</div>
                            </td>
                            <td class="align-middle">{{ $book->publisher?->name }}</td>
                            <td class="align-middle">{{ $book->authors->pluck('name')->join(', ') }}</td>
                            <td class="align-middle">@if($book->price) {{ number_format($book->price, 2, ',', '.') }} € @endif</td>
                            <td class="align-middle">{{ $book->created_at->format('d/m/Y H:i') }}</td>
                            <td class="align-middle">
                                <div class="flex w-28 justify-center gap-1 ml-auto">
                                    <a class="btn btn-square btn-ghost btn-sm" href="{{ route('books.show', $book) }}" aria-label="Ver">
                                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    @auth
                                    <a class="btn btn-square btn-ghost btn-sm" href="{{ route('books.edit', $book) }}" aria-label="Editar">
                                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('books.destroy', $book) }}" method="POST" class="inline-flex">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-square btn-ghost btn-sm text-error hover:bg-error hover:text-white" onclick="return confirm('Eliminar {{ $book->name }}?')" aria-label="Eliminar">
                                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                    @endauth
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="py-10 text-center text-sm opacity-60">Nenhum livro encontrado.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($books->hasPages())
        {{ $books->links() }}
    @endif
</div>
