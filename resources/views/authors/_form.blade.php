@php
  $isEdit = isset($author);
@endphp

<form method="POST" action="{{ $isEdit ? route('authors.update', $author) : route('authors.store') }}" enctype="multipart/form-data">
  @csrf
  @if($isEdit) @method('PUT') @endif

  <div class="mb-4">
    <label class="label">
      <span class="label-text">Nome</span>
    </label>
    <input type="text" name="name" value="{{ old('name', $author->name ?? '') }}" class="input input-bordered w-full" required>
    @error('name') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
  </div>

  <div class="mb-4">
    <label class="label">
      <span class="label-text">Foto (opcional)</span>
    </label>

    @if($isEdit && $author->photo_url)
      <div class="mb-3">
        <img src="{{ $author->photo_url }}" alt="photo" class="w-24 mb-2 rounded" />
        <label class="label cursor-pointer justify-start gap-2">
          <input type="checkbox" name="remove_photo" value="1" class="checkbox checkbox-sm" />
          <span class="label-text text-sm text-error">Remover foto</span>
        </label>
      </div>
    @endif

    <input type="file" name="photo" class="file-input file-input-bordered w-full" accept="image/*" />
    @error('photo') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
  </div>

  <div class="flex gap-2">
    @if($isEdit)
      <button class="btn btn-lg bg-orange-500 hover:bg-orange-600 border-orange-500 px-6 py-3" type="submit" style="color: white !important;">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="white" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
        </svg>
        Actualizar
      </button>
    @else
      <button class="btn btn-lg bg-green-500 hover:bg-green-600 border-green-500 px-6 py-3" type="submit" style="color: white !important;">
        <svg class="w-5 h-5 mr-1" fill="none" stroke="white" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Criar
      </button>
    @endif
    
    <a href="{{ route('authors.index') }}" class="btn btn-outline">
      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
      </svg>
      Cancelar
    </a>
  </div>
</form>
