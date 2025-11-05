<div class="space-y-4">
    @if($isAdmin && $stats)
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
        <div class="stat bg-base-200 rounded-lg shadow">
            <div class="stat-title">Requisições Ativas</div>
            <div class="stat-value text-primary">{{ $stats['active'] }}</div>
        </div>
        <div class="stat bg-base-200 rounded-lg shadow">
            <div class="stat-title">Pendentes de Confirmação</div>
            <div class="stat-value text-warning">{{ $stats['pending'] }}</div>
        </div>
        <div class="stat bg-base-200 rounded-lg shadow">
            <div class="stat-title">Próximas do Vencimento</div>
            <div class="stat-value text-info">{{ $stats['due_soon'] }}</div>
        </div>
        <div class="stat bg-base-200 rounded-lg shadow">
            <div class="stat-title">Atrasadas</div>
            <div class="stat-value text-error">{{ $stats['overdue'] }}</div>
        </div>
    </div>
    @endif

    <div class="card bg-base-100 shadow-xl overflow-x-auto">
        <div class="card-body p-0">
            <div class="px-3 py-3 flex items-center justify-between gap-2">
                <h2 class="text-lg font-semibold whitespace-nowrap">Requisições</h2>
                <div class="flex items-center gap-2 flex-1 justify-end min-w-0">
                    @if($isAdmin)
                    <select class="select select-bordered select-xs min-w-0 w-auto flex-none shrink-0" wire:model.live="statusFilter">
                        <option value="">Todos os Status</option>
                        <option value="pending">Pendente</option>
                        <option value="active">Ativa</option>
                        <option value="returned">Devolvida</option>
                        <option value="cancelled">Cancelada</option>
                    </select>
                    @endif
                    <label class="input input-bordered flex items-center gap-2 min-w-[42rem] md:min-w-[58rem] lg:min-w-[70rem] max-w-full">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4 opacity-70">
                            <path fill-rule="evenodd" d="M10.5 3.75a6.75 6.75 0 1 0 4.16 12.06l3.77 3.77a.75.75 0 1 0 1.06-1.06l-3.77-3.77A6.75 6.75 0 0 0 10.5 3.75Zm-5.25 6.75a5.25 5.25 0 1 1 10.5 0 5.25 5.25 0 0 1-10.5 0Z" clip-rule="evenodd"/>
                        </svg>
                        <input type="text" class="grow" placeholder="Pesquisa por núm, livro ou cidadão." wire:model.live.debounce.300ms="search" />
                        @if($search !== '')
                            <button type="button" class="btn btn-xs btn-ghost" wire:click="$set('search','')">Limpar</button>
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
                        <th>Livro</th>
                        <th>Cidadão</th>
                        <th class="whitespace-nowrap">
                            <button class="link link-hover font-semibold cursor-pointer" wire:click="sortBy('request_date')">
                                Data Requisição
                                @if($sortField === 'request_date')
                                    <span class="ml-1">{{ $sortDir === 'asc' ? '▲' : '▼' }}</span>
                                @endif
                            </button>
                        </th>
                        <th class="whitespace-nowrap">
                            <button class="link link-hover font-semibold cursor-pointer" wire:click="sortBy('expected_return_date')">
                                Data Fim Prevista
                                @if($sortField === 'expected_return_date')
                                    <span class="ml-1">{{ $sortDir === 'asc' ? '▲' : '▼' }}</span>
                                @endif
                            </button>
                        </th>
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
                            <td class="align-middle">{{ $submission->expected_return_date->format('d/m/Y') }}</td>
                            <td class="align-middle">
                                @if($submission->status === 'pending')
                                    <span class="badge badge-warning">Pendente</span>
                                @elseif($submission->status === 'active')
                                    <span class="badge badge-success">Ativa</span>
                                @elseif($submission->status === 'returned')
                                    <span class="badge badge-neutral">Devolvida</span>
                                @else
                                    <span class="badge badge-error">Cancelada</span>
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
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
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