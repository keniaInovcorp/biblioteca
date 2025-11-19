<?php

namespace App\Livewire;

use App\Models\Review;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ReviewsTable extends Component
{
    use WithPagination;

    #[Url(as: 'page', except: 1)]
    public int $page = 1;

    #[Url(as: 'sort')]
    public string $sortField = 'created_at';

    #[Url(as: 'dir')]
    public string $sortDir = 'desc';

    #[Url(as: 'per_page')]
    public int $perPage = 5;

    public $successMessage = '';

    public function mount(): void
    {
        if (!Gate::allows('moderateAny', Review::class)) {
            abort(403);
        }
    }

    public function updatingSortField(): void { $this->resetPage(); }
    public function updatingSortDir(): void { $this->resetPage(); }
    public function updatingPerPage(): void { $this->resetPage(); }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDir = 'desc';
        }
        $this->resetPage();
    }

    public function render()
    {
        $allowedSorts = ['created_at', 'rating'];
        $sortField = in_array($this->sortField, $allowedSorts, true) ? $this->sortField : 'created_at';
        $sortDir = strtolower($this->sortDir) === 'asc' ? 'asc' : 'desc';

        $reviews = Review::with(['user', 'book'])
            ->orderBy($sortField, $sortDir)
            ->paginate($this->perPage);

        // Statistics
        $stats = [
            'pending' => Review::where('status', 'pending')->count(),
            'active' => Review::where('status', 'active')->count(),
            'rejected' => Review::where('status', 'rejected')->count(),
        ];

        return view('livewire.reviews-table', [
            'reviews' => $reviews,
            'sortField' => $sortField,
            'sortDir' => $sortDir,
            'stats' => $stats,
        ]);
    }
}

