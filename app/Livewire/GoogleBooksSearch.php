<?php

namespace App\Livewire;

use App\Services\GoogleBooksService;
use Livewire\Component;
use Livewire\Attributes\Layout as LivewireLayout;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

#[LivewireLayout('layouts.app')]
class GoogleBooksSearch extends Component
{
    use WithPagination;

    #[Url(as: 'page', except: 1)]
    public int $page = 1;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'sort')]
    public string $sortField = 'title';

    #[Url(as: 'dir')]
    public string $sortDir = 'asc';

    #[Url(as: 'per_page')]
    public int $perPage = 10;
    
    #[Url(as: 'adv')]
    public bool $showAdvanced = false;
    
    #[Url(as: 'title')]
    public string $advTitle = '';
    
    #[Url(as: 'author')]
    public string $advAuthor = '';
    
    #[Url(as: 'isbn')]
    public string $advIsbn = '';
    
    #[Url(as: 'publisher')]
    public string $advPublisher = '';
    
    #[Url(as: 'year')]
    public string $advYear = '';

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingPerPage(): void { $this->resetPage(); }
    public function updatingAdvTitle(): void { $this->resetPage(); }
    public function updatingAdvAuthor(): void { $this->resetPage(); }
    public function updatingAdvIsbn(): void { $this->resetPage(); }
    public function updatingAdvPublisher(): void { $this->resetPage(); }
    public function updatingAdvYear(): void { $this->resetPage(); }

    public function toggleAdvanced(): void
    {
        $this->showAdvanced = !$this->showAdvanced;
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        $allowedFields = ['title', 'author', 'publisher', 'year'];
        if (!in_array($field, $allowedFields)) return;

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
        $service = app(GoogleBooksService::class);
        
        $query = '';
        $hasOnlyYear = false;
        if ($this->showAdvanced) {
            $parts = [];
            if ($this->advTitle) $parts[] = 'title:' . $this->advTitle;
            if ($this->advAuthor) $parts[] = 'autor:' . $this->advAuthor;
            if ($this->advIsbn) $parts[] = 'isbn:' . $this->advIsbn;
            if ($this->advPublisher) $parts[] = 'publisher:' . $this->advPublisher;
            
            if (empty($parts) && !empty($this->advYear)) {
                $hasOnlyYear = true;
                $query = 'book'; 
            } else {
                $query = implode(' ', $parts);
            }
        } else {
            $query = $this->search;
        }

        $allResults = [];
        if ($query !== '') {
            if ($this->showAdvanced && !empty($this->advYear)) {
                $rawResults = [];
                $maxPages = $hasOnlyYear ? 10 : 5;
                
                for ($i = 0; $i < $maxPages; $i++) {
                    $resp = $service->searchWithTotal($query, 100, $i * 100);
                    $pageResults = $resp['items'] ?? [];
                    if (empty($pageResults)) break;
                    $rawResults = array_merge($rawResults, $pageResults);
                    
                    $tempFiltered = collect($rawResults)->filter(function ($item) {
                        $publishedDate = $item['volumeInfo']['publishedDate'] ?? '';
                        $year = substr($publishedDate, 0, 4);
                        return $year === $this->advYear;
                    });
                    
                    if ($tempFiltered->count() >= 100) break;
                }
                
                $filtered = collect($rawResults)->filter(function ($item) {
                    $publishedDate = $item['volumeInfo']['publishedDate'] ?? '';
                    $year = substr($publishedDate, 0, 4);
                    return $year === $this->advYear;
                })->values();

                $allResults = $this->sortResults($filtered->all());
            } else {
                $startIndex = max(0, ($this->page - 1) * $this->perPage);
                $resp = $service->searchWithTotal($query, min($this->perPage * 10, 100), 0);
                $rawResults = $resp['items'] ?? [];
                
                $allResults = $this->sortResults($rawResults);
            }
        }

        $total = count($allResults);
        $currentPageResults = collect($allResults)
            ->slice(($this->page - 1) * $this->perPage, $this->perPage)
            ->values()
            ->all();

        $books = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentPageResults,
            $total,
            $this->perPage,
            $this->page,
            ['path' => request()->url(), 'pageName' => 'page']
        );

        return view('livewire.google-books-search', [
            'books' => $books,
            'hasSearch' => $query !== '',
        ]);
    }

    protected function sortResults(array $results): array
    {
        if (empty($results)) return $results;

        $collection = collect($results);
        $sortDir = $this->sortDir === 'desc' ? 'desc' : 'asc';

        switch ($this->sortField) {
            case 'title':
                $collection = $collection->sortBy(fn($item) => $item['volumeInfo']['title'] ?? '', SORT_NATURAL, $sortDir === 'desc');
                break;
            case 'author':
                $collection = $collection->sortBy(function($item) {
                    $authors = $item['volumeInfo']['authors'] ?? [];
                    return !empty($authors) ? $authors[0] : '';
                }, SORT_NATURAL, $sortDir === 'desc');
                break;
            case 'publisher':
                $collection = $collection->sortBy(fn($item) => $item['volumeInfo']['publisher'] ?? '', SORT_NATURAL, $sortDir === 'desc');
                break;
            case 'year':
                $collection = $collection->sortBy(function($item) {
                    $date = $item['volumeInfo']['publishedDate'] ?? '';
                    return substr($date, 0, 4);
                }, SORT_REGULAR, $sortDir === 'desc');
                break;
        }

        return $collection->values()->all();
    }
}
