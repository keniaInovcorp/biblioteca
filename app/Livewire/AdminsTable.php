<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class AdminsTable extends Component
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

    #[Url(as: 'per_page')]
    public int $perPage = 5;

    public function updatingSearch(): void
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
        $allowedSorts = ['name', 'email', 'created_at'];
        $sortField = in_array($this->sortField, $allowedSorts, true) ? $this->sortField : 'name';
        $sortDir = strtolower($this->sortDir) === 'desc' ? 'desc' : 'asc';

        $term = "%{$this->search}%";

        $query = User::role('admin')
            ->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                  ->orWhere('email', 'like', $term);
            });

        // Ordenação case-insensitive
        if ($sortField === 'name' || $sortField === 'email') {
            $query->orderByRaw("LOWER(`{$sortField}`) {$sortDir}");
        } else {
            $query->orderBy($sortField, $sortDir);
        }

        $admins = $query->paginate($this->perPage);

        return view('livewire.admins-table', [
            'admins' => $admins,
        ]);
    }
}