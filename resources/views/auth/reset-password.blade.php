<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="mb-4 text-sm text-gray-600">
            {{ __('Defina a sua password para aceder à sua conta.') }}
        </div>

        <x-validation-errors class="mb-4" />

        @php
            $token = old('token');
            if (!$token && $request->route('token')) {
                $token = $request->route('token');
            }
            if (!$token) {
                $token = request()->query('token');
            }
            if (!$token) {
                $segments = request()->segments();
                $token = $segments[1] ?? null;
            }
        @endphp

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="block">
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Nova Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-label for="password_confirmation" value="{{ __('Confirmar Password') }}" />
                <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button>
                    {{ __('Definir Password') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
