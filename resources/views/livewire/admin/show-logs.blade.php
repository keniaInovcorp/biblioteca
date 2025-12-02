<div class="p-6 bg-base-200 min-h-screen">
    <div class="overflow-x-auto bg-base-100 rounded-lg shadow">
        <table class="table table-zebra w-full">
            <thead>
                <tr>
                    <th>Data/Hora</th>
                    <th>User</th>
                    <th>Módulo</th>
                    <th>ID Objeto</th>
                    <th>Ação</th>
                    <th>IP</th>
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
