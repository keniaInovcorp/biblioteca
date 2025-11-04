<div class="space-y-4">
    <div class="card bg-base-100 shadow-xl overflow-x-auto">
        <div class="card-body p-0">
            <div class="px-3 py-3 flex items-center justify-between gap-2 whitespace-nowrap">
                <h2 class="text-lg font-semibold">Administradores</h2>
                <div class="flex items-center gap-2 flex-1 justify-end">
                    <label class="input input-bordered flex items-center gap-2 w-[32rem] md:w-[48rem] lg:w-[60rem]">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4 opacity-70">
                            <path fill-rule="evenodd" d="M10.5 3.75a6.75 6.75 0 1 0 4.16 12.06l3.77 3.77a.75.75 0 1 0 1.06-1.06l-3.77-3.77A6.75 6.75 0 0 0 10.5 3.75Zm-5.25 6.75a5.25 5.25 0 1 1 10.5 0 5.25 5.25 0 0 1-10.5 0Z" clip-rule="evenodd"/>
                        </svg>
                        <input type="text" class="grow" placeholder="Pesquisar por nome ou email..." wire:model.live.debounce.300ms="search" />
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
                            <button class="link link-hover font-semibold cursor-pointer" wire:click="sortBy('name')">
                                Nome
                                @if($sortField === 'name')
                                    <span class="ml-1">{{ $sortDir === 'asc' ? '▲' : '▼' }}</span>
                                @endif
                            </button>
                        </th>
                        <th>
                            <button class="link link-hover font-semibold cursor-pointer" wire:click="sortBy('email')">
                                Email
                                @if($sortField === 'email')
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
                    @forelse($admins as $admin)
                        <tr>
                            <td class="align-middle">
                                <div class="flex items-center gap-2">
                                    @if($admin->profile_photo_url)
                                        <img class="size-8 rounded-full object-cover" src="{{ $admin->profile_photo_url }}" alt="{{ $admin->name }}" />
                                    @else
                                        <div class="avatar placeholder">
                                            <div class="bg-primary text-primary-content rounded-full size-8">
                                                <span class="text-xs font-bold">{{ substr($admin->name, 0, 1) }}</span>
                                            </div>
                                        </div>
                                    @endif
                                    <span class="font-semibold">{{ $admin->name }}</span>
                                </div>
                            </td>
                            <td class="align-middle">{{ $admin->email }}</td>
                            <td class="align-middle">{{ $admin->created_at->format('d/m/Y H:i') }}</td>
                            <td class="align-middle">
                                <div class="flex w-28 justify-center gap-1 ml-auto">
                                    <a class="btn btn-square btn-ghost btn-sm" href="{{ route('admins.show', $admin) }}" aria-label="Ver">
                                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    @can('delete', $admin)
                                    <form action="{{ route('admins.destroy', $admin) }}" method="POST" class="inline-flex">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-square btn-ghost btn-sm text-error hover:bg-error hover:text-white" onclick="return confirm('Eliminar {{ $admin->name }}?')" aria-label="Eliminar">
                                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="py-10 text-center text-sm opacity-60">Nenhum administrador encontrado.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($admins->hasPages())
        {{ $admins->links() }}
    @endif
</div>