<?php

namespace App\Livewire;

use App\Events\SubmissionCreated;
use App\Models\Book;
use App\Models\Submission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class BooksTable extends Component
{
    use WithPagination;

    #[Url(as: 'page', except: 1)]
    public int $page = 1;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'sfield')]
    public string $searchField = 'all';

    #[Url(as: 'sort')]
    public string $sortField = 'name';

    #[Url(as: 'dir')]
    public string $sortDir = 'asc';

    #[Url(as: 'per_page')]
    public int $perPage = 5;

    public $successMessage = '';
    public $errorMessage = '';

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingSearchField(): void { $this->resetPage(); }
    public function updatingPerPage(): void { $this->resetPage(); }

    public function requestBook($bookId)
    {
        $this->successMessage = '';
        $this->errorMessage = '';

        if (!Gate::allows('create', Submission::class)) {
            $this->errorMessage = 'Você não tem permissão para criar requisições.';
            return;
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Validate limit of 3 active submissions
        $activeSubmissionsCount = $user->activeSubmissions()->count();
        if ($activeSubmissionsCount >= 3) {
            $this->errorMessage = 'Você já tem 3 requisições ativas. Devolva um livro antes de requisitar outro.';
            return;
        }

        $book = Book::findOrFail($bookId);

        // Check availability
        if (!$book->isAvailable()) {
            $this->errorMessage = 'Este livro não está disponível para requisição.';
            return;
        }

        // Create submission
        $submission = Submission::create([
            'request_number' => Submission::generateRequestNumber(),
            'user_id' => $user->id,
            'book_id' => $book->id,
            'request_date' => now(),
            'expected_return_date' => now()->addDays(5),
            'status' => 'created',
        ]);

        $this->successMessage = 'Requisição criada com sucesso!';
        $this->resetPage(); // Refresh the table
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
        $allowedSorts = ['name', 'price', 'publisher_name', 'authors_min_name'];
        $sortField = in_array($this->sortField, $allowedSorts, true) ? $this->sortField : 'name';
        $sortDir = strtolower($this->sortDir) === 'desc' ? 'desc' : 'asc';

        $term = "%{$this->search}%";
        $query = Book::query()
            ->with(['publisher', 'authors'])
            ->select('books.*')
            ->when($this->search !== '', function ($q) use ($term) {
                if ($this->searchField === 'name') {
                    $q->where('books.name', 'like', $term);
                } elseif ($this->searchField === 'publisher') {
                    $q->whereHas('publisher', fn($p) => $p->where('name', 'like', $term));
                } elseif ($this->searchField === 'author') {
                    $q->whereHas('authors', fn($a) => $a->where('name', 'like', $term));
                } elseif ($this->searchField === 'price') {
                    $numeric = preg_replace('/[^0-9.,-]/', '', $this->search);
                    $numeric = str_replace(',', '.', $numeric);
                    if (str_contains($numeric, '-')) {
                        // Price between 10-20
                        [$min, $max] = array_map('trim', explode('-', $numeric, 2));
                        if ($min !== '' && $max !== '') {
                            $q->where('price', '>=', (float)$min)
                              ->where('price', '<=', (float)$max);
                        }
                    } elseif ($numeric !== '') {
                        // Exact value: 12.5
                        $q->where('price', (float)$numeric);
                    }
                } else {
                    $q->where(function ($qq) use ($term) {
                        $qq->where('books.name', 'like', $term)
                           ->orWhere('isbn', 'like', $term)
                           ->orWhereHas('publisher', fn($p) => $p->where('name', 'like', $term))
                           ->orWhereHas('authors', fn($a) => $a->where('name', 'like', $term));
                    });
                }
            });

        if ($sortField === 'publisher_name') {
            $query->leftJoin('publishers', 'publishers.id', '=', 'books.publisher_id')
                ->addSelect('publishers.name as publisher_name')
                ->orderBy('publisher_name', $sortDir);
        } elseif ($sortField === 'authors_min_name') {
            $query->selectSub(
                'SELECT MIN(authors.name) FROM book_author ba JOIN authors ON authors.id = ba.author_id WHERE ba.book_id = books.id',
                'authors_min_name'
            )->orderBy('authors_min_name', $sortDir);
        } else {
            $query->orderBy($sortField, $sortDir);
        }

        $books = $query->paginate($this->perPage);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $canRequestMore = $user ? $user->activeSubmissions()->count() < 3 : false;

        return view('livewire.books-table', [
            'books' => $books,
            'canRequestMore' => $canRequestMore,
        ]);
    }
}
