@php
    /** @var \App\Models\Submission $submission */
    $book = $submission->book;
    $user = $submission->user;
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Requisição') }} #{{ $submission->request_number }}
        </h2>
    </x-slot>

    <div class="max-w-8xl mx-auto px-4 py-8">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <div class="flex gap-6 items-start">
                    @if($book?->cover_image_url)
                        <img src="{{ url($book->cover_image_url) }}" alt="{{ $book->name }}" class="w-40 h-auto rounded">
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
                        <div>
                            <div class="text-sm text-gray-500">{{ __('Número') }}</div>
                            <div class="font-medium">{{ $submission->request_number }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">{{ __('Livro') }}</div>
                            <div class="font-medium">{{ $book?->name }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">{{ __('Cidadão') }}</div>
                            <div class="font-medium">{{ $user?->name }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">{{ __('Data da requisição') }}</div>
                            <div class="font-medium">{{ $submission->request_date?->format('d/m/Y') }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">{{ __('Data prevista de devolução') }}</div>
                            <div class="font-medium">{{ $submission->expected_return_date?->format('d/m/Y') }}</div>
                        </div>
                        @if($submission->received_at)
                            <div>
                                <div class="text-sm text-gray-500">{{ __('Data real de entrega') }}</div>
                                <div class="font-medium">{{ $submission->received_at?->format('d/m/Y') }}</div>
                            </div>
                        @endif
                        <div>
                            <div class="text-sm text-gray-500">{{ __('Estado') }}</div>
                            <div>
                                @php
                                    $statusColors = [
                                        'created' => 'badge-info',
                                        'approved' => 'badge-primary',
                                        'rejected' => 'badge-warning',
                                        'returned' => 'badge-success',
                                    ];
                                @endphp
                                <span class="badge {{ $statusColors[$submission->status] ?? 'badge-ghost' }}">
                                    {{ ucfirst(__($submission->status)) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-actions justify-end mt-6">
                    <a href="{{ route('submissions.index') }}" class="btn btn-ghost">{{ __('Voltar') }}</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


