<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthorRequest;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $authors = Author::orderBy('name')->paginate(5);
        return view('authors.index', compact('authors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('authors.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AuthorRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('authors/photos', 'public');
            $data['photo_path'] = $path;
        }

        Author::create($data);

        return redirect()
            ->route('authors.index')
            ->with('success', 'Autor criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Author $author)
    {
        return view('authors.show', compact('author'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Author $author)
    {
        return view('authors.edit', compact('author'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AuthorRequest $request, Author $author)
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            
            if ($author->photo_path && Storage::disk('public')->exists($author->photo_path)) {
                Storage::disk('public')->delete($author->photo_path);
            }

            $path = $request->file('photo')->store('authors/photos', 'public');
            $data['photo_path'] = $path;
        }

        $author->update($data);

        return redirect()
            ->route('authors.index')
            ->with('success', 'Autor atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Author $author)
    {
        if ($author->photo_path && Storage::disk('public')->exists($author->photo_path)) {
            Storage::disk('public')->delete($author->photo_path);
        }

        $author->delete();

        return redirect()
            ->route('authors.index')
            ->with('success', 'Autor removido com sucesso.');
    }
}
