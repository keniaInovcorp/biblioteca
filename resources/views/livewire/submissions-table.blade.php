<div class="space-y-4">
    @if($isAdmin && $stats)
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <!-- Card:Active Requests -->
        <div class="card bg-base-100 shadow-lg">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-base-content/70">Requisições Ativas</h3>
                        <p class="text-3xl font-bold text-primary mt-2 text-center">{{ $stats['active'] }}</p>
                    </div>
                    <div class="flex items-center justify-center w-16 h-16 rounded-full bg-primary/10">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-primary">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card: Requests in the last 30 days -->
        <div class="card bg-base-100 shadow-lg">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-base-content/70">Requisições nos últimos 30 dias</h3>
                        <p class="text-3xl font-bold text-info mt-2 text-center">{{ $stats['last_30_days'] }}</p>
                    </div>
                    <div class="flex items-center justify-center w-16 h-16 rounded-full bg-info/10">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-info">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card: Books delivered today -->
        <div class="card bg-base-100 shadow-lg">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-base-content/70">Livros entregues Hoje</h3>
                        <p class="text-3xl font-bold text-success mt-2 text-center">{{ $stats['returned_today'] }}</p>
                    </div>
                    <div class="flex items-center justify-center w-16 h-16 rounded-full bg-success/10">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-success">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="card bg-base-100 shadow-xl overflow-x-auto">
        <div class="card-body p-0">
            <div class="px-3 py-3 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                <h2 class="text-lg font-semibold whitespace-nowrap">Requisições</h2>
                <div class="flex items-center gap-2 w-full sm:w-auto sm:flex-1 sm:justify-end">
                    @if($isAdmin)
                    <select class="select select-bordered flex-none shrink-0 sm:w-[180px]" wire:model.live="statusFilter">
                        <option value="">Todos os Status</option>
                        <option value="created">Criada</option>
                        <option value="overdue">Atrasada</option>
                        <option value="returned">Devolvida</option>
                    </select>
                    @endif
                    <label class="input input-bordered flex items-center gap-2 flex-1 sm:flex-initial sm:min-w-[300px]">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4 opacity-70">
                            <path fill-rule="evenodd" d="M10.5 3.75a6.75 6.75 0 1 0 4.16 12.06l3.77 3.77a.75.75 0 1 0 1.06-1.06l-3.77-3.77A6.75 6.75 0 0 0 10.5 3.75Zm-5.25 6.75a5.25 5.25 0 1 1 10.5 0 5.25 5.25 0 0 1-10.5 0Z" clip-rule="evenodd"/>
                        </svg>
                        <input type="text" class="grow min-w-0" placeholder="Pesquisar..." wire:model.live.debounce.300ms="search" />
                        @if($search !== '')
                            <button type="button" class="btn btn-xs btn-ghost flex-none shrink-0" wire:click="$set('search','')">Limpar</button>
                        @endif
                    </label>
                </div>
            </div>
            <table class="table table-zebra table-sm">
                <thead>
                    <tr>
                        <th>
                            <button class="link link-hover font-semibold cursor-pointer" wire:click="sortBy('request_number')">
                                Número
                                @if($sortField === 'request_number')
                                    <span class="ml-1">{{ $sortDir === 'asc' ? '▲' : '▼' }}</span>
                                @endif
                            </button>
                        </th>
                        <th>
                            <button class="link link-hover font-semibold cursor-pointer" wire:click="sortBy('book_name')">
                                Livro
                                @if($sortField === 'book_name')
                                    <span class="ml-1">{{ $sortDir === 'asc' ? '▲' : '▼' }}</span>
                                @endif
                            </button>
                        </th>
                        <th>
                            <button class="link link-hover font-semibold cursor-pointer" wire:click="sortBy('user_name')">
                                Cidadão
                                @if($sortField === 'user_name')
                                    <span class="ml-1">{{ $sortDir === 'asc' ? '▲' : '▼' }}</span>
                                @endif
                            </button>
                        </th>
                        <th class="whitespace-nowrap">
                            <button class="link link-hover font-semibold cursor-pointer" wire:click="sortBy('request_date')">
                                Data Requisição
                                @if($sortField === 'request_date')
                                    <span class="ml-1">{{ $sortDir === 'asc' ? '▲' : '▼' }}</span>
                                @endif
                            </button>
                        </th>
                        @if($isAdmin)
                            <th class="whitespace-nowrap">
                                <button class="link link-hover font-semibold cursor-pointer" wire:click="sortBy('received_at')">
                                    Data Real de Entrega
                                    @if($sortField === 'received_at')
                                        <span class="ml-1">{{ $sortDir === 'asc' ? '▲' : '▼' }}</span>
                                    @endif
                                </button>
                            </th>
                            <th class="whitespace-nowrap">
                                <button class="link link-hover font-semibold cursor-pointer" wire:click="sortBy('days_elapsed')">
                                    Dias Reais
                                    @if($sortField === 'days_elapsed')
                                        <span class="ml-1">{{ $sortDir === 'asc' ? '▲' : '▼' }}</span>
                                    @endif
                                </button>
                            </th>
                        @else
                            <th class="whitespace-nowrap">
                                <button class="link link-hover font-semibold cursor-pointer" wire:click="sortBy('expected_return_date')">
                                    Data Fim Prevista
                                    @if($sortField === 'expected_return_date')
                                        <span class="ml-1">{{ $sortDir === 'asc' ? '▲' : '▼' }}</span>
                                    @endif
                                </button>
                            </th>
                        @endif
                        <th>
                            <button class="link link-hover font-semibold cursor-pointer" wire:click="sortBy('status')">
                                Status
                                @if($sortField === 'status')
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
                    @forelse($submissions as $submission)
                        <tr>
                            <td class="align-middle font-semibold">{{ $submission->request_number }}</td>
                            <td class="align-middle">
                                <div class="flex items-center gap-2">
                                    @if($submission->book->cover_image_url)
                                        <img src="{{ $submission->book->cover_image_url }}" alt="{{ $submission->book->name }}" class="w-8 h-8 rounded object-cover" />
                                    @endif
                                    <span>{{ $submission->book->name }}</span>
                                </div>
                            </td>
                            <td class="align-middle">
                                <div class="flex items-center gap-2">
                                    @if($submission->user->profile_photo_url)
                                        <img src="{{ $submission->user->profile_photo_url }}" alt="{{ $submission->user->name }}" class="w-6 h-6 rounded-full object-cover" />
                                    @else
                                        <div class="avatar placeholder">
                                            <div class="bg-primary text-primary-content rounded-full w-6">
                                                <span class="text-xs">{{ substr($submission->user->name, 0, 1) }}</span>
                                            </div>
                                        </div>
                                    @endif
                                    <span>{{ $submission->user->name }}</span>
                                </div>
                            </td>
                            <td class="align-middle">{{ $submission->request_date->format('d/m/Y') }}</td>
                            @if($isAdmin)
                                <td class="align-middle">
                                    @if($submission->received_at)
                                        {{ $submission->received_at->format('d/m/Y') }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    @if($submission->days_elapsed !== null)
                                        @if($submission->days_elapsed < 5)
                                            <span class="badge badge-success">{{ $submission->days_elapsed }} {{ $submission->days_elapsed === 1 ? 'dia' : 'dias' }}</span>
                                        @else
                                            <span class="badge badge-error">{{ $submission->days_elapsed }} {{ $submission->days_elapsed === 1 ? 'dia' : 'dias' }}</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            @else
                                <td class="align-middle">{{ $submission->expected_return_date->format('d/m/Y') }}</td>
                            @endif
                            <td class="align-middle">
                                @php
                                    $effectiveStatus = $submission->effective_status;
                                @endphp
                                @if($effectiveStatus === 'created')
                                    <span class="badge bg-gray-500 text-white">Criada</span>
                                @elseif($effectiveStatus === 'overdue')
                                    <span class="badge badge-error">Atrasada</span>
                                @elseif($effectiveStatus === 'returned')
                                    <span class="badge badge-success">Devolvida</span>
                                @endif
                            </td>
                            <td class="align-middle">
                                <div class="flex w-28 justify-center gap-1 ml-auto">
                                    <a class="btn btn-square btn-ghost btn-sm" href="{{ route('submissions.show', $submission) }}" aria-label="Ver">
                                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    @if($isAdmin && $effectiveStatus !== 'returned')
                                        <a href="#" class="btn btn-square btn-ghost btn-sm" wire:click.prevent="confirmReturn({{ $submission->id }})" aria-label="Confirmar Devolução" title="Confirmar Devolução">
                                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isAdmin ? '8' : '7' }}">
                                <div class="py-10 text-center text-sm opacity-60">Nenhuma requisição encontrada.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($submissions->hasPages())
        {{ $submissions->links() }}
    @endif
</div>