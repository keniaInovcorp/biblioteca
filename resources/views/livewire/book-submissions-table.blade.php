<div>
<div class="card bg-base-100 shadow-xl overflow-x-auto mt-6">
    <div class="card-body p-0">
        <div class="px-3 py-3">
            <h2 class="text-lg font-semibold">Histórico de Requisições</h2>
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
                    @if($isAdmin)
                    <th>
                        <button class="link link-hover font-semibold cursor-pointer" wire:click="sortBy('user_name')">
                            Cidadão
                            @if($sortField === 'user_name')
                                <span class="ml-1">{{ $sortDir === 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </button>
                    </th>
                    @endif
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
                    @if($isAdmin)
                    <th class="whitespace-nowrap">
                        <button class="link link-hover font-semibold cursor-pointer" wire:click="sortBy('received_at')">
                            Data Real de Entrega
                            @if($sortField === 'received_at')
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
                </tr>
            </thead>
            <tbody>
                @forelse($submissions as $submission)
                    <tr>
                        <td class="align-middle font-semibold">{{ $submission->request_number }}</td>
                        @if($isAdmin)
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
                        @endif
                        <td class="align-middle">{{ $submission->request_date->format('d/m/Y') }}</td>
                        <td class="align-middle">{{ $submission->expected_return_date->format('d/m/Y') }}</td>
                        @if($isAdmin)
                        <td class="align-middle">
                            @if($submission->received_at)
                                {{ $submission->received_at->format('d/m/Y') }}
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
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
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $isAdmin ? '6' : '4' }}">
                            <div class="py-10 text-center text-sm opacity-60">Nenhuma requisição encontrada para este livro.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($submissions->hasPages())
        <div class="px-3 py-3">
            {{ $submissions->links() }}
        </div>
    @endif
</div>
</div>

