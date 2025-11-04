<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ver Administrador</h2>
            <a href="{{ route('admins.index') }}" class="btn">Voltar</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="flex gap-4 items-start">
                    @if($admin->profile_photo_url)
                        <img src="{{ $admin->profile_photo_url }}" alt="{{ $admin->name }}" class="w-32 rounded-full" />
                    @else
                        <div class="avatar placeholder">
                            <div class="bg-primary text-primary-content rounded-full w-32">
                                <span class="text-4xl font-bold">{{ substr($admin->name, 0, 1) }}</span>
                            </div>
                        </div>
                    @endif

                    <div class="flex-1">
                        <h3 class="text-2xl font-bold">{{ $admin->name }}</h3>
                        <div class="mt-2 space-y-1 text-sm">
                            <p><span class="font-semibold">Email:</span> {{ $admin->email }}</p>
                            <p><span class="font-semibold">Role:</span>
                                @if($admin->hasRole('admin'))
                                    <span class="badge badge-primary">Admin</span>
                                @endif
                            </p>
                            <p class="text-sm text-gray-500 mt-4">Criado a {{ $admin->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>