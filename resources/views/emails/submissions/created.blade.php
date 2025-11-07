@component('mail::message')
# {{ __('Nova Requisição') }}

{{ __('Olá :name,', ['name' => $user->name]) }}

{{ __('A sua requisição **:number** foi criada com sucesso.', ['number' => $submission->request_number]) }}

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
**{{ __('Data da requisição') }}:** {{ $submission->request_date?->format('d/m/Y') }}
**{{ __('Data prevista de devolução') }}:** {{ $submission->expected_return_date?->format('d/m/Y') }}
@endcomponent



{{ __('Obrigada,') }}
{{ config('app.name') }}
@endcomponent
