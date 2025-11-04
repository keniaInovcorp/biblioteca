<form method="POST" action="{{ route('admins.store') }}">
    @csrf

    <div class="mb-4">
        <label class="label">
            <span class="label-text">Nome <span class="text-error">*</span></span>
        </label>
        <input type="text" name="name" value="{{ old('name') }}" class="input input-bordered w-full" required>
        @error('name') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="mb-4">
        <label class="label">
            <span class="label-text">Email <span class="text-error">*</span></span>
        </label>
        <input type="email" name="email" value="{{ old('email') }}" class="input input-bordered w-full" required>
        @error('email') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="alert alert-info mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span>O administrador receberá um email para definir a sua password.</span>
    </div>

    <div class="flex gap-2">
        <button class="btn btn-lg bg-green-500 hover:bg-green-600 border-green-500 px-6 py-3" type="submit" style="color: white !important;">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="white" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Criar
        </button>

        <a href="{{ route('admins.index') }}" class="btn btn-outline">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Cancelar
        </a>
    </div>
</form>