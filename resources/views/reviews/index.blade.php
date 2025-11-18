<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Moderação de Reviews
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session()->has('success'))
                <div class="alert alert-success mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Cidadão</th>
                                    <th>Livro</th>
                                    <th>Avaliação</th>
                                    <th>Status</th>
                                    <th>Data</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reviews as $review)
                                    <tr>
                                        <td>{{ $review->user->name }}</td>
                                        <td>{{ $review->book->name }}</td>
                                        <td>
                                            <div class="rating rating-sm">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <input
                                                        type="radio"
                                                        class="mask mask-star-2 bg-orange-400"
                                                        @if($i <= $review->rating) checked @endif
                                                        disabled
                                                    />
                                                @endfor
                                            </div>
                                        </td>
                                        <td>
                                            @if($review->status === 'pending')
                                                <span class="badge badge-warning">Pendente</span>
                                            @elseif($review->status === 'active')
                                                <span class="badge badge-success">Ativo</span>
                                            @else
                                                <span class="badge badge-error">Rejeitado</span>
                                            @endif
                                        </td>
                                        <td>{{ $review->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('reviews.show', $review) }}" class="btn btn-sm btn-primary">
                                                Ver Detalhes
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-8">Nenhuma review encontrada.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $reviews->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>