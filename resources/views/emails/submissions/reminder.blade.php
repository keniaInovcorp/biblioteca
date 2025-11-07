@component('mail::message')
# {{ __('Lembrete de Devolução') }}

{{ __('Olá :name,', ['name' => $user->name]) }}

{{ __('Este é um lembrete de que a devolução do livro abaixo é amanhã.') }}

@if(!empty($coverCid) || !empty($coverUrl))
<div style="margin: 16px 0; text-align: center;">
    @if(!empty($coverCid))
        <img src="cid:{{ $coverCid }}" alt="{{ $book->name }}" style="max-width: 200px; border-radius: 8px;">
    @else
        <img src="{{ $coverUrl }}" alt="{{ $book->name }}" style="max-width: 200px; border-radius: 8px;">
    @endif
</div>
@endif

@component('mail::panel')
**{{ __('Livro') }}:** {{ $book->name }}
**{{ __('Data prevista de devolução') }}:** {{ $submission->expected_return_date?->format('d/m/Y') }}
@endcomponent

{{ __('Obrigada,') }}
{{ config('app.name') }}
@endcomponent
