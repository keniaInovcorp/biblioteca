<x-app-layout>
    <x-slot name="header">
        <div class="navbar bg-base-100">
            <div class="flex-1">
                <h1 class="text-2xl font-bold flex items-center gap-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    Editoras
                </h1>
            </div>
            <div class="flex-none">
                @auth
                    <a href="{{ route('publishers.create') }}" class="btn btn-primary gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Adicionar Editora
                    </a>
                @endauth
            </div>
        </div>
    </x-slot>

    <div class="container mx-auto px-4 py-8">
        <!-- Live table -->
        <livewire:publishers-table />

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


        <!-- Publishers List -->
        @if($publishers->count() > 0)
            

        @else
            <!-- Empty State - Minimal & Beautiful -->
            <div class="flex flex-col items-center justify-center py-12">
                <div class="text-center space-y-4 max-w-sm">
                    <!-- Small elegant icon -->
                    <div class="w-12 h-12 mx-auto bg-primary/10 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    
                    <!-- Simple text -->
                    <div>
                        <h3 class="text-lg font-semibold">Nenhuma editora</h3>
                        <p class="text-sm opacity-60">Adicione a primeira editora à sua biblioteca</p>
                    </div>
                    
                    <!-- Clean button -->
                    @auth
                        <a href="{{ route('publishers.create') }}" class="btn btn-primary btn-sm gap-2">
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