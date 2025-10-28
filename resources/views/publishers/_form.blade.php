@php
  $isEdit = isset($publisher);
@endphp

<form method="POST" action="{{ $isEdit ? route('publishers.update', $publisher) : route('publishers.store') }}" enctype="multipart/form-data">
  @csrf
  @if($isEdit) @method('PUT') @endif

  <div class="mb-4">
    <label class="label">
      <span class="label-text">Nome</span>
    </label>
    <input type="text" name="name" value="{{ old('name', $publisher->name ?? '') }}" class="input input-bordered w-full" required>
    @error('name') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
  </div>

  <div class="mb-4">
    <label class="label">
      <span class="label-text">Logótipo (opcional)</span>
    </label>

    @if($isEdit && $publisher->logo_url)
      <img src="{{ $publisher->logo_url }}" alt="logo" class="w-24 mb-2 rounded" />
    @endif

    <input type="file" name="logo" class="file-input file-input-bordered w-full" accept="image/*" />
    @error('logo') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
  </div>

  <div class="flex gap-2">
    <button class="btn btn-primary" type="submit">{{ $isEdit ? 'Actualizar' : 'Criar' }}</button>
    <a href="{{ route('publishers.index') }}" class="btn">Cancelar</a>
  </div>
</form>
