<?php

namespace App\Http\Controllers;

use App\Services\GoogleBooksService;
use App\Http\Requests\GoogleBooksSearchRequest;
use App\Http\Requests\GoogleBooksImportOneRequest;
use App\Http\Requests\GoogleBooksImportByQueryRequest;
use App\Http\Requests\GoogleBooksShowRequest;
use Illuminate\Http\Request;

class GoogleBooksController extends Controller
{
    public function search(GoogleBooksSearchRequest $request, GoogleBooksService $googleBooksService)
    {
        $validated = $request->validated();

        $maxResults = (int) ($validated['maxResults'] ?? 10);

        switch ($validated['type']) {
            case 'isbn':
                $query = 'isbn:' . $validated['isbn'];
                $items = $googleBooksService->search($query, $maxResults);
                break;
            case 'title':
                $query = 'intitle:' . $validated['q'];
                $items = $googleBooksService->search($query, $maxResults);
                break;
            default:
                $items = $googleBooksService->search($validated['q'], $maxResults);
        }

        return response()->json([
            'count' => count($items),
            'items' => $items,
        ]);
    }

    public function show(GoogleBooksShowRequest $request, GoogleBooksService $googleBooksService)
    {
        $validated = $request->validated();
        $volume = $googleBooksService->fetchVolumeById($validated['id']);
        if (!$volume) {
            return response()->json(['message' => 'Volume não encontrado'], 404);
        }
        return response()->json($volume);
    }

    public function importOne(GoogleBooksImportOneRequest $request, GoogleBooksService $googleBooksService)
    {
        $validated = $request->validated();

        if (!empty($validated['id'])) {
            $volume = $googleBooksService->fetchVolumeById($validated['id']);
        } else {
            $items = $googleBooksService->search('isbn:' . $validated['isbn'], 1);
            $volume = $items[0] ?? null;
        }

        if (!$volume) {
            return response()->json(['message' => 'Volume não encontrado'], 404);
        }

        $book = $googleBooksService->importVolume($volume);
        if (!$book) {
            return response()->json(['message' => 'Não foi possível importar este volume'], 422);
        }

        return response()->json([
            'book_id' => $book->id,
            'isbn' => $book->isbn,
        ]);
    }

    public function importByQuery(GoogleBooksImportByQueryRequest $request, GoogleBooksService $googleBooksService)
    {
        $validated = $request->validated();

        $result = $googleBooksService->importByQuery(
            $validated['q'],
            (int) ($validated['maxResults'] ?? 10)
        );

        return response()->json($result);
    }
}


