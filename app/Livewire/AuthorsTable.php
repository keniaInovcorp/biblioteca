<?php

namespace App\Livewire;

use App\Models\Author;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class AuthorsTable extends Component
{
    use WithPagination;

    #[Url(as: 'page', except: 1)]
    public int $page = 1;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'sort')]
    public string $sortField = 'name';

    #[Url(as: 'dir')]
    public string $sortDir = 'asc';

    #[Url(as: 'has_photo')]
    public string $hasPhoto = '';

    #[Url(as: 'per_page')]
    public int $perPage = 5;

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingHasPhoto(): void { $this->resetPage(); }
    public function updatingPerPage(): void { $this->resetPage(); }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDir = 'asc';
        }
        $this->resetPage();
    }

    public function render()
    {
        $allowedSorts = ['name', 'created_at'];
        $sortField = in_array($this->sortField, $allowedSorts, true) ? $this->sortField : 'name';
        $sortDir = strtolower($this->sortDir) === 'desc' ? 'desc' : 'asc';

        $authors = Author::query()
            ->when($this->search !== '', fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->hasPhoto === '1', fn($q) => $q->whereNotNull('photo_path'))
            ->when($this->hasPhoto === '0', fn($q) => $q->whereNull('photo_path'))
            ->orderBy($sortField, $sortDir)
            ->paginate($this->perPage);

        return view('livewire.authors-table', [
            'authors' => $authors,
        ]);
    }
}


