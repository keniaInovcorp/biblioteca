<?php

namespace App\Livewire;

use App\Models\Book;
use App\Models\Submission;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class BookSubmissionsTable extends Component
{
    use WithPagination;

    public Book $book;

    #[Url(as: 'page', except: 1)]
    public int $page = 1;

    #[Url(as: 'sort')]
    public string $sortField = 'request_date';

    #[Url(as: 'dir')]
    public string $sortDir = 'desc';

    #[Url(as: 'per_page')]
    public int $perPage = 10;

    public function mount(Book $book)
    {
        $this->book = $book;
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
        $allowedSorts = ['request_number', 'request_date', 'expected_return_date', 'received_at', 'status', 'user_name'];
        $sortField = in_array($this->sortField, $allowedSorts, true) ? $this->sortField : 'request_date';
        $sortDir = strtolower($this->sortDir) === 'desc' ? 'desc' : 'asc';

        $user = Auth::user();
        /** @var \App\Models\User $user */
        $isAdmin = $user->hasRole('admin');

        $query = Submission::with(['user'])
            ->join('users', 'submissions.user_id', '=', 'users.id')
            ->select('submissions.*')
            ->where('submissions.book_id', $this->book->id);

        // If not admin, filter only requests from the current user
        if (!$isAdmin) {
            $query->where('submissions.user_id', $user->id);
        }

        // Handle sorting
        if ($sortField === 'request_number') {
            $query->orderByRaw("LOWER(submissions.{$sortField}) {$sortDir}");
        } elseif ($sortField === 'user_name') {
            $query->orderByRaw("LOWER(users.name) {$sortDir}");
        } else {
            $query->orderBy("submissions.{$sortField}", $sortDir);
        }

        $submissions = $query->paginate($this->perPage);

        return view('livewire.book-submissions-table', [
            'submissions' => $submissions,
            'isAdmin' => $isAdmin,
        ]);
    }
}

