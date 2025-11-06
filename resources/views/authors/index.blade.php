<x-app-layout>
    <x-slot name="header">
        <div class="navbar bg-base-100">
            <div class="flex-1">
                <h1 class="text-2xl font-bold flex items-center gap-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Autores
                </h1>
            </div>
            <div class="flex-none">
                @auth
                    <a href="{{ route('authors.create') }}" class="btn btn-primary gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Adicionar Autor
                    </a>
                @endauth
            </div>
        </div>
    </x-slot>

    <div class="max-w-8xl mx-auto px-4 py-8">
        <livewire:authors-table />

        <!-- Alert Success -->
        @if(session('success'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3500)" x-show="show" x-transition class="alert alert-success shadow-lg mb-6">
                <div>
                    <svg class="stroke-current flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(false)
            
            <div class="flex flex-col items-center justify-center py-12">
                <div class="text-center space-y-4 max-w-sm">
                    <!-- Small icon -->
                    <div class="w-12 h-12 mx-auto bg-primary/10 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    
                    <!-- Simple text -->
                    <div>
                        <h3 class="text-lg font-semibold">Nenhum autor</h3>
                        <p class="text-sm opacity-60">Adicione o primeiro autor à sua biblioteca</p>
                    </div>
                    
                    <!-- Clean button -->
                    @auth
                        <a href="{{ route('authors.create') }}" class="btn btn-primary btn-sm gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Adicionar
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline btn-sm">
                            Iniciar Sessão
                        </a>
                    @endauth
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
