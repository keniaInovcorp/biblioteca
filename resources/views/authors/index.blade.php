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

    <div class="container mx-auto px-4 py-8">
        
        <!-- Alert Success -->
        @if(session('success'))
            <div class="alert alert-success shadow-lg mb-6">
                <div>
                    <svg class="stroke-current flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <!-- Authors List -->
        @if($authors->count() > 0)
            <div class="card bg-white shadow-xl">
                <div class="card-body p-0">
                    <ul class="list bg-white rounded-box">
                        
                        <li class="p-4 pb-2 text-xs opacity-60 tracking-wide uppercase">
                            Autores da biblioteca ({{ $authors->total() }} {{ $authors->total() == 1 ? 'autor' : 'autores' }})
                        </li>
                        
                        @foreach($authors as $author)
                        <li class="list-row hover:bg-base-50 transition-colors">
                            <!-- Photo/Avatar -->
                            <div>
                                @if($author->photo_url)
                                    <img class="size-12 rounded-box object-cover" src="{{ $author->photo_url }}" alt="{{ $author->name }}"/>
                                @else
                                    <div class="avatar placeholder">
                                        <div class="bg-primary text-primary-content rounded-box size-12">
                                            <span class="text-lg font-bold">{{ substr($author->name, 0, 1) }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Author Info -->
                            <div class="flex-1">
                                <div class="font-semibold text-base">{{ $author->name }}</div>
                                <div class="text-xs opacity-60 flex items-center gap-2">
                                    <span class="badge badge-ghost badge-xs">ID: {{ $author->id }}</span>
                                    <span>•</span>
                                    <span>Criado {{ $author->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="flex gap-1 items-center">
                                <!-- View Button -->
                                <button type="button" 
                                        class="btn btn-square btn-ghost btn-sm tooltip" 
                                        data-tip="Ver detalhes"
                                        onclick="window.location.href='{{ route('authors.show', $author) }}'">
                                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                                
                                @auth
                                <!-- Edit Button -->
                                <button type="button" 
                                        class="btn btn-square btn-ghost btn-sm tooltip" 
                                        data-tip="Editar"
                                        onclick="window.location.href='{{ route('authors.edit', $author) }}'">
                                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                
                                <!-- Delete Button -->
                                <form action="{{ route('authors.destroy', $author) }}" method="POST" class="contents">
                                    @csrf @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-square btn-ghost btn-sm tooltip text-error hover:bg-error hover:text-white" 
                                            data-tip="Eliminar"
                                            onclick="return confirm('Eliminar {{ $author->name }}?')">
                                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                                @endauth
                            </div>
                        </li>
                        @endforeach
                        
                    </ul>
                </div>
            </div>

            <!-- Pagination -->
            @if($authors->hasPages())
                <div class="flex justify-center mt-8" id="pagination-wrapper">
                    {{ $authors->links() }}
                </div>
            @endif

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const paginationWrapper = document.getElementById('pagination-wrapper');
                    if (paginationWrapper) {
                        const textElement = paginationWrapper.querySelector('p');
                        if (textElement) {
                            const textContainer = document.createElement('div');
                            textContainer.className = 'flex justify-center mt-3';
                            
                            const clonedText = textElement.cloneNode(true);
                            const originalText = clonedText.textContent;
                            
                            const translatedText = originalText
                                .replace(/Showing/g, 'Mostrando')
                                .replace(/to/g, 'a')
                                .replace(/of/g, 'de')
                                .replace(/results/g, 'resultados');
                            
                            clonedText.textContent = translatedText;
                            textContainer.appendChild(clonedText);
                            
                            textElement.style.display = 'none';
                            
                            paginationWrapper.parentNode.insertBefore(textContainer, paginationWrapper.nextSibling);
                        }
                    }
                });
            </script>

        @else
            <!-- Empty State - Minimal & Beautiful -->
            <div class="flex flex-col items-center justify-center py-12">
                <div class="text-center space-y-4 max-w-sm">
                    <!-- Small elegant icon -->
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
