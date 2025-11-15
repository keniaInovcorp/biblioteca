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

    public function search(string $query, int $maxResults = 20, int $startIndex = 0): array
    {
        $parsedQuery = $this->parseQuery($query);
        $params = [
            'q' => $this->buildApiQuery($parsedQuery),
            'maxResults' => max(1, min($maxResults, 40)),
            'projection' => 'full',
            'printType' => 'books',
            'startIndex' => max(0, $startIndex),
        ];
        if (!empty($this->apiKey)) {
            $params['key'] = $this->apiKey;
        }
        $cacheKey = 'gb:search:' . md5($query . ':' . json_encode($params));
        if (Cache::has($cacheKey)) {
            Log::info('gb.search.cache_hit', ['q' => $params['q']]);
            return Cache::get($cacheKey, []);
        }

        try {
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
            $items = Arr::get($response->json(), 'items', []);
            Cache::put($cacheKey, $items, now()->addMinutes(15));
            Log::info('gb.search.cache_miss', [
                'q' => $params['q'],
                'status' => $response->status(),
                'duration_ms' => $durationMs,
            ]);
            return $this->filterItems($items, $parsedQuery);
        } catch (\Throwable $e) {
            Log::error('gb.search.exception', [
                'q' => $params['q'] ?? null,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    public function searchWithTotal(string $query, int $maxResults = 20, int $startIndex = 0): array
    {
        $parsedQuery = $this->parseQuery($query);
        $apiQuery = $this->buildApiQuery($parsedQuery);

        Log::info('gb.search.parsed', [
            'user_query' => $query,
            'parsed' => $parsedQuery,
            'api_query' => $apiQuery,
        ]);

        $params = [
            'q' => $apiQuery,
            'maxResults' => max(1, min($maxResults, 40)),
            'projection' => 'full',
            'printType' => 'books',
            'startIndex' => max(0, $startIndex),
        ];
        if (!empty($this->apiKey)) {
            $params['key'] = $this->apiKey;
        }
        $cacheKey = 'gb:search-total:' . md5($query . ':' . json_encode($params));
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey, ['items' => [], 'total' => 0]);
        }

        try {
            $response = Http::timeout(10)
                ->retry(3, 200)
                ->get("{$this->baseUrl}/volumes", $params);
            if (!$response->ok()) {
                Log::warning('gb.search_total.not_ok', [
                    'status' => $response->status(),
                    'api_query' => $apiQuery,
                ]);
                return ['items' => [], 'total' => 0];
            }
            $json = $response->json();
            $totalItems = (int) Arr::get($json, 'totalItems', 0);
            $rawItems = Arr::get($json, 'items', []);

            Log::info('gb.search_total.api_response', [
                'total_items' => $totalItems,
                'raw_count' => count($rawItems),
            ]);

            if ($totalItems > 1000) {
                $totalItems = 1000;
            }
            $items = $this->filterItems($rawItems, $parsedQuery);
            $filteredCount = count($items);

            Log::info('gb.search_total.after_filter', [
                'filtered_count' => $filteredCount,
            ]);

            $cappedTotal = min($totalItems, 1000);
            if ($filteredCount < count($rawItems) && $startIndex === 0) {
                $cappedTotal = min($cappedTotal, max($filteredCount, $filteredCount + ($totalItems - count($rawItems))));
            }
            $payload = [
                'items' => $items,
                'total' => $cappedTotal,
            ];
            Cache::put($cacheKey, $payload, now()->addMinutes(10));
            return $payload;
        } catch (\Throwable $e) {
            Log::error('gb.search_total.exception', [
                'q' => $apiQuery,
                'error' => $e->getMessage(),
            ]);
            return ['items' => [], 'total' => 0];
        }
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

        $price = $this->extractPrice($volume);

        $book = Book::firstOrNew(['isbn' => $isbn]);
        $book->name = $title;
        $book->publisher_id = $publisher->id;

        if ($description !== '') {
            $book->bibliography = $description;
        }
        if ($coverPath && empty($book->cover_image_path)) {
            $book->cover_image_path = $coverPath;
        }
        if ($price !== null && (empty($book->price) || $book->price == 0)) {
            $book->price = $price;
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

    protected function extractPrice(array $volume): ?float
    {
        $saleInfo = Arr::get($volume, 'saleInfo', []);
        if (!is_array($saleInfo) || empty($saleInfo)) {
            return null;
        }

        // Verificar se o livro está à venda
        $saleability = Arr::get($saleInfo, 'saleability', '');
        if ($saleability !== 'FOR_SALE') {
            return null;
        }

        // Priorizar retailPrice depois listPrice
        $retailPrice = Arr::get($saleInfo, 'retailPrice', []);
        $listPrice = Arr::get($saleInfo, 'listPrice', []);

        $priceData = !empty($retailPrice) ? $retailPrice : $listPrice;

        if (empty($priceData) || !is_array($priceData)) {
            return null;
        }

        $amount = Arr::get($priceData, 'amount');
        if ($amount === null || $amount === '') {
            return null;
        }

        $price = (float) $amount;
        if ($price <= 0 || $price > 999999.99) {
            return null;
        }

        return round($price, 2);
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

    protected function parseQuery(string $raw): array
    {
        $raw = trim($raw);
        $data = [
            'raw' => $raw,
            'titles' => [],
            'authors' => [],
            'isbns' => [],
            'publishers' => [],
            'keywords' => [],
        ];

        if ($raw === '') {
            return $data;
        }

        $mappings = [
            'title' => 'titles',
            'titulo' => 'titles',
            'autor' => 'authors',
            'author' => 'authors',
            'isbn' => 'isbns',
            'isn' => 'isbns',
            'publisher' => 'publishers',
            'editora' => 'publishers',
        ];

        $remaining = $raw;
        foreach ($mappings as $alias => $bucket) {
            $pattern = sprintf('/%s\s*:\s*("[^"]+"|[^\s]+)/iu', preg_quote($alias, '/'));
            $remaining = preg_replace_callback($pattern, function ($matches) use (&$data, $bucket, $alias) {
                $value = trim($matches[1] ?? '');
                $value = trim($value, '"');

                if ($value === '') {
                    return ' ';
                }
                if ($bucket === 'isbns') {
                    $digits = preg_replace('/[^0-9Xx]/', '', $value);
                    Log::info('gb.parse.isbn_attempt', [
                        'raw_value' => $value,
                        'digits' => $digits,
                        'length' => strlen($digits),
                    ]);
                    if ($digits !== '' && (strlen($digits) === 10 || strlen($digits) === 13)) {
                        $data['isbns'][] = Str::upper($digits);
                    }
                } else {
                    $data[$bucket][] = $value;
                }
                return ' ';
            }, $remaining);
        }

        $remaining = trim(preg_replace('/\s+/', ' ', $remaining));
        if ($remaining !== '') {
            $tokens = preg_split('/\s+/', $remaining);

            foreach ($tokens as $token) {
                $token = trim($token);
                if ($token === '') continue;

                $digits = preg_replace('/[^0-9Xx]/', '', $token);
                if (strlen($digits) === 10 || strlen($digits) === 13) {
                    Log::info('gb.parse.auto_isbn', [
                        'token' => $token,
                        'digits' => $digits,
                    ]);
                    $data['isbns'][] = Str::upper($digits);
                } else {
                    $data['keywords'][] = $token;
                }
            }
        }

        return $data;
    }

    protected function buildApiQuery(array $parsed): string
    {
        $allTerms = [];

        foreach ($parsed['titles'] as $title) {
            $allTerms[] = sprintf('intitle:"%s"', addcslashes($title, '"'));
        }

        foreach ($parsed['authors'] as $author) {
            $allTerms[] = sprintf('inauthor:"%s"', addcslashes($author, '"'));
        }

        foreach ($parsed['publishers'] as $publisher) {
            $allTerms[] = sprintf('inpublisher:"%s"', addcslashes($publisher, '"'));
        }

        foreach ($parsed['isbns'] as $isbn) {
            $allTerms[] = sprintf('isbn:%s', $isbn);
        }

        foreach ($parsed['keywords'] as $keyword) {
            $escaped = addcslashes($keyword, '"');
            $allTerms[] = sprintf('intitle:"%s"', $escaped);
            $allTerms[] = sprintf('inauthor:"%s"', $escaped);
            if (preg_match('/\d+/', $keyword)) {
                $allTerms[] = sprintf('isbn:%s', $keyword);
            }
        }

        if (empty($allTerms)) {
            return '""';
        }

        if (count($allTerms) === 1) {
            return $allTerms[0];
        }

        return '(' . implode('+OR+', $allTerms) . ')';
    }

    protected function filterItems(array $items, array $parsed): array
    {
        if (empty($parsed['titles']) && empty($parsed['authors']) && empty($parsed['isbns'])) {
            return $items;
        }

        if (!empty($parsed['isbns'])) {
            return $items;
        }

        $titleFilters = array_map(fn ($v) => Str::lower($v), $parsed['titles']);
        $authorFilters = array_map(fn ($v) => Str::lower($v), $parsed['authors']);

        return collect($items)->filter(function ($item) use ($titleFilters, $authorFilters) {
            $info = Arr::get($item, 'volumeInfo', []);
            $title = Str::lower((string) Arr::get($info, 'title', ''));
            $authors = collect(Arr::get($info, 'authors', []))
                ->filter()
                ->map(fn ($name) => Str::lower((string) $name));

            foreach ($titleFilters as $filter) {
                if ($filter !== '' && !Str::contains($title, $filter)) {
                    return false;
                }
            }

            foreach ($authorFilters as $filter) {
                if ($filter === '') {
                    continue;
                }
                $match = $authors->contains(fn ($name) => Str::contains($name, $filter));
                if (!$match) {
                    return false;
                }
            }

            return true;
        })->values()->all();
    }

    protected function volumeIsbns(array $volumeInfo): array
    {
        $identifiers = Arr::get($volumeInfo, 'industryIdentifiers', []);
        if (!is_array($identifiers)) {
            return [];
        }
        return collect($identifiers)
            ->map(function ($identifier) {
                $value = $identifier['identifier'] ?? null;
                if (!$value) {
                    return null;
                }
                $digits = preg_replace('/[^0-9Xx]/', '', (string) $value);
                if ($digits === '' || (strlen($digits) !== 10 && strlen($digits) !== 13)) {
                    return null;
                }
                return Str::upper($digits);
            })
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
