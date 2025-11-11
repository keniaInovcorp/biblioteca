<?php

namespace App\Services;

use App\Models\Author;
use App\Models\Book;
use App\Models\Publisher;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GoogleBooksService
{
    protected string $baseUrl;
    protected ?string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.google_books.base_url', 'https://www.googleapis.com/books/v1');
        $this->apiKey = config('services.google_books.key');
    }

    public function search(string $query, int $maxResults = 20): array
    {
        $params = [
            'q' => $query,
            'maxResults' => max(1, min($maxResults, 40)),
            'projection' => 'full',
            'printType' => 'books',
        ];
        if (!empty($this->apiKey)) {
            $params['key'] = $this->apiKey;
        }
        $cacheKey = 'gb:search:' . md5(json_encode($params));
        if (Cache::has($cacheKey)) {
            Log::info('gb.search.cache_hit', ['q' => $params['q']]);
        }
        return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($params) {
            $start = microtime(true);
            $response = Http::timeout(10)
                ->retry(3, 200)
                ->get("{$this->baseUrl}/volumes", $params);
            $durationMs = (int) ((microtime(true) - $start) * 1000);
            if (!$response->ok()) {
                Log::warning('gb.search.failed', [
                    'q' => $params['q'],
                    'status' => $response->status(),
                    'duration_ms' => $durationMs,
                ]);
                return [];
            }
            Log::info('gb.search.cache_miss', [
                'q' => $params['q'],
                'status' => $response->status(),
                'duration_ms' => $durationMs,
            ]);
            return Arr::get($response->json(), 'items', []);
        });
    }

    public function fetchVolumeById(string $volumeId): ?array
    {
        $params = [
            'projection' => 'full',
        ];
        if (!empty($this->apiKey)) {
            $params['key'] = $this->apiKey;
        }
        $cacheKey = "gb:volume:{$volumeId}:" . md5(json_encode($params));
        if (Cache::has($cacheKey)) {
            Log::info('gb.volume.cache_hit', ['id' => $volumeId]);
        }
        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($volumeId, $params) {
            $start = microtime(true);
            $response = Http::timeout(10)
                ->retry(3, 200)
                ->get("{$this->baseUrl}/volumes/{$volumeId}", $params);
            $durationMs = (int) ((microtime(true) - $start) * 1000);
            if (!$response->ok()) {
                Log::warning('gb.volume.failed', [
                    'id' => $volumeId,
                    'status' => $response->status(),
                    'duration_ms' => $durationMs,
                ]);
                return null;
            }
            Log::info('gb.volume.cache_miss', [
                'id' => $volumeId,
                'status' => $response->status(),
                'duration_ms' => $durationMs,
            ]);
            return $response->json();
        });
    }

    public function importByQuery(string $query, int $maxResults = 20): array
    {
        $items = $this->search($query, $maxResults);
        $imported = 0;
        $skipped = 0;
        foreach ($items as $item) {
            $book = $this->importVolume($item);
            $book ? $imported++ : $skipped++;
        }
        return ['imported' => $imported, 'skipped' => $skipped];
    }

    public function importVolume(array $volume): ?Book
    {
        $volumeInfo = Arr::get($volume, 'volumeInfo', []);
        if (empty($volumeInfo)) {
            return null;
        }

        // If description is missing in search results, fetch full volume and retry
        $description = (string) Arr::get($volumeInfo, 'description', '');
        if ($description === '' && isset($volume['id'])) {
            $full = $this->fetchVolumeById((string) $volume['id']);
            if (is_array($full)) {
                $volumeInfo = Arr::get($full, 'volumeInfo', $volumeInfo);
                $description = (string) Arr::get($volumeInfo, 'description', $description);
            }
        }

        $isbn = $this->extractIsbn($volumeInfo);
        if (empty($isbn)) {
            return null;
        }
        $title = trim((string) Arr::get($volumeInfo, 'title', ''));
        if ($title === '') {
            return null;
        }
        $publisherName = trim((string) Arr::get($volumeInfo, 'publisher', '')) ?: 'Desconhecido';
        $publisher = $this->firstOrCreatePublisher($publisherName);
        $authorNames = Arr::get($volumeInfo, 'authors', []);
        $authors = $this->syncAuthors(is_array($authorNames) ? $authorNames : []);
        $imageUrl = $this->bestImageUrl($volumeInfo);
        $coverPath = $imageUrl ? $this->downloadCover($imageUrl, $isbn) : null;

        $book = Book::firstOrNew(['isbn' => $isbn]);
        $book->name = $title;
        $book->publisher_id = $publisher->id;

        if ($description !== '') {
            $book->bibliography = $description;
        }
        if ($coverPath && empty($book->cover_image_path)) {
            $book->cover_image_path = $coverPath;
        }
        $book->save();

        if ($authors->isNotEmpty()) {
            $book->authors()->syncWithoutDetaching($authors->pluck('id')->all());
        }
        return $book;
    }

    protected function extractIsbn(array $volumeInfo): ?string
    {
        $identifiers = Arr::get($volumeInfo, 'industryIdentifiers', []);
        if (!is_array($identifiers)) return null;
        $isbn13 = collect($identifiers)->first(fn ($id) => ($id['type'] ?? null) === 'ISBN_13');
        $isbn10 = collect($identifiers)->first(fn ($id) => ($id['type'] ?? null) === 'ISBN_10');
        $value = $isbn13['identifier'] ?? $isbn10['identifier'] ?? null;
        if (!$value) return null;
        $clean = preg_replace('/[^0-9Xx]/', '', (string) $value);
        return $clean ?: null;
    }

    protected function firstOrCreatePublisher(string $name): Publisher
    {
        $normalized = trim($name);
        $existing = Publisher::whereRaw('LOWER(name) = ?', [Str::lower($normalized)])->first();
        return $existing ?: Publisher::create(['name' => $normalized]);
    }

    protected function syncAuthors(array $names)
    {
        $authors = collect();
        foreach ($names as $rawName) {
            $name = trim((string) $rawName);
            if ($name === '') continue;
            $existing = Author::whereRaw('LOWER(name) = ?', [Str::lower($name)])->first();
            if (!$existing) {
                $existing = Author::create(['name' => $name]);
            }
            $authors->push($existing);
        }
        return $authors->unique('id')->values();
    }

    protected function bestImageUrl(array $volumeInfo): ?string
    {
        $links = Arr::get($volumeInfo, 'imageLinks', []);
        if (!is_array($links) || empty($links)) return null;
        foreach (['extraLarge','large','medium','small','thumbnail','smallThumbnail'] as $k) {
            if (!empty($links[$k])) return (string) $links[$k];
        }
        return null;
    }

    protected function downloadCover(string $url, string $isbn): ?string
    {
        try {
            $start = microtime(true);
            $response = Http::timeout(10)
                ->retry(2, 200)
                ->get($url);
            if (!$response->ok()) return null;
            $extension = str_contains((string) $response->header('Content-Type'), 'png') ? 'png' : 'jpg';
            $filename = 'books/covers/' . $isbn . '.' . $extension;
            Storage::disk('public')->put($filename, $response->body());
            Log::info('gb.cover.saved', [
                'isbn' => $isbn,
                'bytes' => strlen($response->body() ?? ''),
                'duration_ms' => (int) ((microtime(true) - $start) * 1000),
            ]);
            return $filename;
        } catch (\Throwable $e) {
            Log::error('gb.cover.error', [
                'isbn' => $isbn,
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
