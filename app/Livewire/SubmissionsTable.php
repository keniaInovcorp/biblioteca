<?php

namespace App\Livewire;

use App\Models\Submission;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class SubmissionsTable extends Component
{
    use WithPagination;

    #[Url(as: 'page', except: 1)]
    public int $page = 1;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'sort')]
    public string $sortField = 'request_date';

    #[Url(as: 'dir')]
    public string $sortDir = 'desc';

    #[Url(as: 'status')]
    public string $statusFilter = '';

    #[Url(as: 'per_page')]
    public int $perPage = 10;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
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
            $this->sortDir = 'desc';
        }
        $this->resetPage();
    }

    public function render()
    {
        $allowedSorts = ['request_number', 'request_date', 'expected_return_date', 'status', 'book_id'];
        $sortField = in_array($this->sortField, $allowedSorts, true) ? $this->sortField : 'request_date';
        $sortDir = strtolower($this->sortDir) === 'desc' ? 'desc' : 'asc';

        $term = "%{$this->search}%";
        $user = Auth::user();
        /** @var \App\Models\User $user */
        $isAdmin = $user->hasRole('admin');

        $query = Submission::with(['user', 'book'])
            ->where(function ($q) use ($term, $isAdmin, $user) {
                $q->where('request_number', 'like', $term)
                  ->orWhereHas('book', function ($bookQuery) use ($term) {
                      $bookQuery->where('name', 'like', $term);
                  })
                  ->orWhereHas('user', function ($userQuery) use ($term) {
                      $userQuery->where('name', 'like', $term)
                                ->orWhere('email', 'like', $term);
                  });
            });

        // If you are not an admin, filter only requests from that user.
        if (!$isAdmin) {
            $query->where('user_id', $user->id);
        }

        if ($this->statusFilter !== '') {
            $query->where('status', $this->statusFilter);
        }

        // Case-insensitive sorting for text fields
        if ($sortField === 'request_number') {
            $query->orderByRaw("LOWER({$sortField}) {$sortDir}");
        } else {
            $query->orderBy($sortField, $sortDir);
        }

        $submissions = $query->paginate($this->perPage);

        // Admin statistics
        $stats = null;
        if ($isAdmin) {
            $stats = [
                'active' => Submission::where('status', 'active')->count(),
                'pending' => Submission::where('status', 'pending')->count(),
                'due_soon' => Submission::where('status', 'active')
                    ->whereDate('expected_return_date', '<=', now()->addDays(2))
                    ->whereDate('expected_return_date', '>=', now())
                    ->count(),
                'overdue' => Submission::where('status', 'active')
                    ->whereDate('expected_return_date', '<', now())
                    ->count(),
            ];
        }

        return view('livewire.submissions-table', [
            'submissions' => $submissions,
            'stats' => $stats,
            'isAdmin' => $isAdmin,
        ]);
    }
}