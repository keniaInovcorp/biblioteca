<?php

namespace App\Livewire;

use App\Models\Publisher;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class PublishersTable extends Component
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

    #[Url(as: 'has_logo')]
    public string $hasLogo = '';

    #[Url(as: 'per_page')]
    public int $perPage = 5;

    // Query string binding handled via attributes above

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingHasLogo(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

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

        $publishers = Publisher::query()
            ->when($this->search !== '', function ($q) {
                $q->where('name', 'like', "%{$this->search}%");
            })
            ->when($this->hasLogo === '1', function ($q) {
                $q->whereNotNull('logo_path');
            })
            ->when($this->hasLogo === '0', function ($q) {
                $q->whereNull('logo_path');
            })
            ->orderBy($sortField, $sortDir)
            ->paginate($this->perPage);

        return view('livewire.publishers-table', [
            'publishers' => $publishers,
        ]);
    }
}


