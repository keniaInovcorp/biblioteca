<?php

namespace App\Http\Controllers;

use App\Http\Requests\PublisherRequest;
use App\Models\Publisher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublisherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $publishers = Publisher::orderBy('name')->paginate(5);
        return view('publishers.index', compact('publishers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('publishers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PublisherRequest $request)
    {
       $data = $request->validated();

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('publishers/logos', 'public');
            $data['logo_path'] = $path;
        }

        Publisher::create($data);

        return redirect()
            ->route('publishers.index')
            ->with('success', 'Editora criada com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Publisher $publisher)
    {
         return view('publishers.show', compact('publisher'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Publisher $publisher)
    {
        return view('publishers.edit', compact('publisher'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PublisherRequest $request, Publisher $publisher)
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
         
            if ($publisher->logo_path && Storage::disk('public')->exists($publisher->logo_path)) {
                Storage::disk('public')->delete($publisher->logo_path);
            }

            $path = $request->file('logo')->store('publishers/logos', 'public');
            $data['logo_path'] = $path;
        }

        $publisher->update($data);

        return redirect()
            ->route('publishers.index')
            ->with('success', 'Editora atualizada com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Publisher $publisher)
    {
        if ($publisher->logo_path && Storage::disk('public')->exists($publisher->logo_path)) {
            Storage::disk('public')->delete($publisher->logo_path);
        }

        $publisher->delete();

        return redirect()
            ->route('publishers.index')
            ->with('success', 'Editora removida com sucesso.');
    }
}
