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

    public function confirmReturn(int $submissionId): void
    {
        $user = Auth::user();
        /** @var \App\Models\User $user */
        
        // Only admin can confirm returns
        if (!$user->hasRole('admin')) {
            session()->flash('error', 'Apenas administradores podem confirmar devoluções.');
            return;
        }

        $submission = Submission::findOrFail($submissionId);

        // Can only return if not already returned
        if ($submission->status === 'returned') {
            session()->flash('error', 'Esta requisição já foi devolvida.');
            return;
        }

        // Calculate days elapsed from request_date to now
        $requestDate = $submission->request_date;
        $daysElapsed = $requestDate->diffInDays(now()->startOfDay());

        $submission->update([
            'status' => 'returned',
            'received_at' => now(),
            'days_elapsed' => $daysElapsed,
        ]);

        session()->flash('success', 'Devolução confirmada com sucesso!');
    }

    public function render()
    {
        $allowedSorts = ['request_number', 'request_date', 'expected_return_date', 'received_at', 'days_elapsed', 'status', 'book_id', 'book_name', 'user_name'];
        $sortField = in_array($this->sortField, $allowedSorts, true) ? $this->sortField : 'request_date';
        $sortDir = strtolower($this->sortDir) === 'desc' ? 'desc' : 'asc';

        $term = "%{$this->search}%";
        $user = Auth::user();
        /** @var \App\Models\User $user */
        $isAdmin = $user->hasRole('admin');

        $query = Submission::with(['user', 'book'])
            ->join('books', 'submissions.book_id', '=', 'books.id')
            ->join('users', 'submissions.user_id', '=', 'users.id')
            ->select('submissions.*')
            ->where(function ($q) use ($term, $isAdmin, $user) {
                $q->where('submissions.request_number', 'like', $term)
                  ->orWhere('books.name', 'like', $term)
                  ->orWhere('users.name', 'like', $term)
                  ->orWhere('users.email', 'like', $term);
            });

        // If you are not an admin, filter only requests from that user.
        if (!$isAdmin) {
            $query->where('submissions.user_id', $user->id);
        }

        if ($this->statusFilter !== '') {
            if ($this->statusFilter === 'overdue') {
                // Overdue OR created and past expected date
                $query->where(function ($qq) {
                    $qq->where('submissions.status', 'overdue')
                       ->orWhere(function ($q2) {
                           $q2->where('submissions.status', 'created')
                              ->whereDate('submissions.expected_return_date', '<', now()->startOfDay());
                       });
                });
            } elseif ($this->statusFilter === 'created') {
                // created and NOT past expected date
                $query->where('submissions.status', 'created')
                      ->whereDate('submissions.expected_return_date', '>=', now()->startOfDay());
            } else {
                $query->where('submissions.status', $this->statusFilter);
            }
        }

        // Handle sorting
        if ($sortField === 'request_number') {
            $query->orderByRaw("LOWER(submissions.{$sortField}) {$sortDir}");
        } elseif ($sortField === 'book_name') {
            $query->orderByRaw("LOWER(books.name) {$sortDir}");
        } elseif ($sortField === 'user_name') {
            $query->orderByRaw("LOWER(users.name) {$sortDir}");
        } else {
            $query->orderBy("submissions.{$sortField}", $sortDir);
        }

        $submissions = $query->paginate($this->perPage);

        // Statistics (only for admin)
        $stats = null;
        if ($isAdmin) {
            $stats = [
                'active' => Submission::whereIn('status', ['created', 'overdue'])->count(),
                'last_30_days' => Submission::where('request_date', '>=', now()->subDays(30))->count(),
                'returned_today' => Submission::where('status', 'returned')
                    ->whereDate('received_at', now())
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