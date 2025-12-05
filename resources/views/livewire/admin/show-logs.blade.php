<div class="p-6 bg-base-200 min-h-screen">
    <div class="overflow-x-auto bg-base-100 rounded-lg shadow">
        <table class="table table-zebra w-full">
            <thead>
                <tr>
                    <th>
                        <button class="link link-hover font-semibold cursor-pointer" wire:click="sortBy('created_at')">
                            Data/Hora
                            @if($sortField === 'created_at')
                                <span class="ml-1">{{ $sortDir === 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </button>
                    </th>
                    <th>
                        <button class="link link-hover font-semibold cursor-pointer" wire:click="sortBy('user_name')">
                            User
                            @if($sortField === 'user_name')
                                <span class="ml-1">{{ $sortDir === 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </button>
                    </th>
                    <th>
                        <button class="link link-hover font-semibold cursor-pointer" wire:click="sortBy('module')">
                            Módulo
                            @if($sortField === 'module')
                                <span class="ml-1">{{ $sortDir === 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </button>
                    </th>
                    <th>
                        <button class="link link-hover font-semibold cursor-pointer" wire:click="sortBy('subject_id')">
                            ID Objeto
                            @if($sortField === 'subject_id')
                                <span class="ml-1">{{ $sortDir === 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </button>
                    </th>
                    <th>
                        <button class="link link-hover font-semibold cursor-pointer" wire:click="sortBy('action')">
                            Ação
                            @if($sortField === 'action')
                                <span class="ml-1">{{ $sortDir === 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </button>
                    </th>
                    <th>
                        <button class="link link-hover font-semibold cursor-pointer" wire:click="sortBy('ip_address')">
                            IP
                            @if($sortField === 'ip_address')
                                <span class="ml-1">{{ $sortDir === 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </button>
                    </th>
                    <th>Browser</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                <tr>
                    <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $log->user->name ?? 'Sistema' }}</td>
                    <td><span class="badge badge-outline">{{ $log->module }}</span></td>
                    <td>{{ $log->subject_id }}</td>
                    <td>
                        <span class="badge {{ $log->action == 'created' ? 'badge-success' : ($log->action == 'deleted' ? 'badge-error' : 'badge-warning') }}">
                            {{ $log->action }}
                        </span>
                    </td>
                    <td>{{ $log->ip_address }}</td>
                    <td class="truncate max-w-xs" title="{{ $log->browser }}">
                        {{ Str::limit($log->browser, 20) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4">
            {{ $logs->links() }}
        </div>
    </div>
</div>
