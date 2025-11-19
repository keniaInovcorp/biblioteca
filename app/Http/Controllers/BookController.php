<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookRequest;
use App\Models\Book;
use App\Models\Publisher;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Exports\BooksExport;
use Maatwebsite\Excel\Facades\Excel;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $books = Book::with(['publisher', 'authors'])->orderBy('name')->paginate(5);
        return view('books.index', compact('books'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Book::class);

        $publishers = Publisher::orderBy('name')->get();
        $authors = Author::orderBy('name')->get();
        return view('books.create', compact('publishers', 'authors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BookRequest $request)
    {
        $this->authorize('create', Book::class);
        $data = $request->validated();

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('books/covers', 'public');
            $data['cover_image_path'] = $path;
        }

        // Create the book
        $book = Book::create($data);

        // Sincronizar autores, muitos para muitos
        if ($request->has('authors')) {
            $book->authors()->sync($request->authors);
        }

        return redirect()
            ->route('books.index')
            ->with('success', 'Livro criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        $book->load(['publisher', 'authors', 'activeReviews.user']);
        return view('books.show', compact('book'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Book $book)
    {
        $this->authorize('update', $book);

        $publishers = Publisher::orderBy('name')->get();
        $authors = Author::orderBy('name')->get();
        $book->load('authors');
        return view('books.edit', compact('book', 'publishers', 'authors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BookRequest $request, Book $book)
    {
        $this->authorize('update', $book);
        $data = $request->validated();

        if ($request->has('remove_cover_image') && $request->remove_cover_image == '1') {
            if ($book->cover_image_path && Storage::disk('public')->exists($book->cover_image_path)) {
                Storage::disk('public')->delete($book->cover_image_path);
            }
            $data['cover_image_path'] = null;
        }

        if ($request->hasFile('cover_image')) {
            // Remove imagem antiga
            if ($book->cover_image_path && Storage::disk('public')->exists($book->cover_image_path)) {
                Storage::disk('public')->delete($book->cover_image_path);
            }

            $path = $request->file('cover_image')->store('books/covers', 'public');
            $data['cover_image_path'] = $path;
        }

        $book->update($data);

        if ($request->has('authors')) {
            $book->authors()->sync($request->authors);
        } else {
            // Se não enviar autores, remove todos
            $book->authors()->sync([]);
        }

        return redirect()
            ->route('books.index')
            ->with('success', 'Livro atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        $this->authorize('delete', $book);

        if ($book->cover_image_path && Storage::disk('public')->exists($book->cover_image_path)) {
            Storage::disk('public')->delete($book->cover_image_path);
        }

        $book->delete();

        return redirect()
            ->route('books.index')
            ->with('success', 'Livro removido com sucesso.');
    }

    /**
     * Export books as Excel
     */
    public function export(Request $request)
    {
        $search = (string) $request->query('q', '');
        $searchField = (string) $request->query('sfield', 'all');
        $sortField = (string) $request->query('sort', 'name');
        $sortDir = strtolower((string) $request->query('dir', 'asc')) === 'desc' ? 'desc' : 'asc';

        $allowedSorts = ['name', 'created_at', 'price', 'publisher_name', 'authors_min_name'];
        if (! in_array($sortField, $allowedSorts, true)) {
            $sortField = 'name';
        }

        $term = "%{$search}%";
        $query = Book::query()
            ->with(['publisher', 'authors'])
            ->select('books.*')
            ->when($search !== '', function ($q) use ($term, $searchField, $search) {
                if ($searchField === 'name') {
                    $q->where('books.name', 'like', $term);
                } elseif ($searchField === 'publisher') {
                    $q->whereHas('publisher', fn($p) => $p->where('name', 'like', $term));
                } elseif ($searchField === 'author') {
                    $q->whereHas('authors', fn($a) => $a->where('name', 'like', $term));
                } elseif ($searchField === 'price') {
                    // Price 12.5 or full range 10-20
                    $numeric = preg_replace('/[^0-9.,-]/', '', $search);
                    $numeric = str_replace(',', '.', $numeric);
                    if (str_contains($numeric, '-')) {
                        // price between 10-20
                        [$min, $max] = array_map('trim', explode('-', $numeric, 2));
                        if ($min !== '' && $max !== '') {
                            $q->where('price', '>=', (float)$min)
                              ->where('price', '<=', (float)$max);
                        }
                    } elseif ($numeric !== '') {
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

        $filename = 'livros_' . now()->format('Ymd_His');

        return Excel::download(new BooksExport($query), $filename . '.xlsx');
    }
}
