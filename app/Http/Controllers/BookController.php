<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookRequest;
use App\Models\Book;
use App\Models\Publisher;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        $publishers = Publisher::orderBy('name')->get();
        $authors = Author::orderBy('name')->get();
        return view('books.create', compact('publishers', 'authors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BookRequest $request)
    {
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
        $book->load(['publisher', 'authors']);
        return view('books.show', compact('book'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Book $book)
    {
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
        if ($book->cover_image_path && Storage::disk('public')->exists($book->cover_image_path)) {
            Storage::disk('public')->delete($book->cover_image_path);
        }

        $book->delete();

        return redirect()
            ->route('books.index')
            ->with('success', 'Livro removido com sucesso.');
    }
}
