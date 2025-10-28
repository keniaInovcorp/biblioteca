<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editoras</h2>
      <a href="{{ route('publishers.create') }}" class="btn btn-primary">Adicionar Editora</a>
    </div>
  </x-slot>

  <div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      @if(session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
      @endif

      <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
        <table class="table w-full">
          <thead>
            <tr>
              <th>Logótipo</th>
              <th>Nome</th>
              <th>Criado</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            @forelse($publishers as $publisher)
              <tr>
                <td>
                  @if($publisher->logo_url)
                    <img src="{{ $publisher->logo_url }}" alt="logo" class="w-16 h-auto rounded" />
                  @else
                    <div class="w-16 h-16 flex items-center justify-center bg-base-200 rounded">—</div>
                  @endif
                </td>
                <td>{{ $publisher->name }}</td>
                <td>{{ $publisher->created_at->format('Y-m-d') }}</td>
                <td class="flex gap-2">
                  <a href="{{ route('publishers.show', $publisher) }}" class="btn btn-sm">Ver</a>
                  <a href="{{ route('publishers.edit', $publisher) }}" class="btn btn-sm btn-outline">Editar</a>
                  <form action="{{ route('publishers.destroy', $publisher) }}" method="POST" onsubmit="return confirm('Confirma remover?');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-error" type="submit">Apagar</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr><td colspan="4" class="text-center">Nenhuma editora encontrada.</td></tr>
            @endforelse
          </tbody>
        </table>

        <div class="mt-4">
          {{ $publishers->links() }}
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
