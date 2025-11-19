<?php

namespace App\Services;

use App\Models\Book;
use Illuminate\Support\Str;

class BookSimilarityService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * You can find related books based on similarity of description.
     */
    public function findRelatedBooks(Book $book, int $limit = 5): \Illuminate\Support\Collection
    {
        if (empty($book->bibliography)) {
            return collect([]);
        }

        $bookKeywords = $this->extractKeywords($book->bibliography);

        if ($bookKeywords->isEmpty()) {
            return collect([]);
        }

        // Search for all books except the current one.
        $allBooks = Book::where('id', '!=', $book->id)
            ->whereNotNull('bibliography')
            ->where('bibliography', '!=', '')
            ->get();

        // Calculate similarity for each book.
        $similarities = $allBooks->map(function ($otherBook) use ($bookKeywords) {
            $otherKeywords = $this->extractKeywords($otherBook->bibliography);
            $similarity = $this->calculateSimilarity($bookKeywords, $otherKeywords);

            return [
                'book' => $otherBook,
                'similarity' => $similarity,
            ];
        })
        ->filter(fn($item) => $item['similarity'] > 0)
        ->sortByDesc('similarity')
        ->take($limit)
        ->pluck('book');

        return $similarities;
    }

    /**
     * Extracts relevant keywords from a text.
     */
    protected function extractKeywords(string $text): \Illuminate\Support\Collection
    {
        // Convert to lowercase and remove accents.
        $text = Str::lower($text);
        $text = $this->removeAccents($text);

        // Remove punctuation and split into words.
        $words = preg_split('/\s+/', preg_replace('/[^\w\s]/u', ' ', $text));

        // Filter out very short words and stop words.
        $stopWords = $this->getStopWords();

        $keywords = collect($words)
            ->filter(fn($word) => strlen($word) >= 3)
            ->filter(fn($word) => !in_array($word, $stopWords))
            ->countBy()
            ->sortDesc()
            ->take(20); // Top 20 most frequent words

        return $keywords;
    }

    /**
     * Calculates the similarity between two sets of keywords.
     */
    protected function calculateSimilarity(
        \Illuminate\Support\Collection $keywords1,
        \Illuminate\Support\Collection $keywords2
    ): float {
        if ($keywords1->isEmpty() || $keywords2->isEmpty()) {
            return 0;
        }

        // Keyword intersection
        $intersection = $keywords1->keys()->intersect($keywords2->keys());

        if ($intersection->isEmpty()) {
            return 0;
        }

        // Calculate score based on frequency.
        $score = $intersection->sum(function ($word) use ($keywords1, $keywords2) {
            return min($keywords1[$word], $keywords2[$word]);
        });

        //Normalize (divide by the total number of unique words)
        $totalWords = $keywords1->keys()->merge($keywords2->keys())->unique()->count();

        return $totalWords > 0 ? ($score / $totalWords) * 100 : 0;
    }

    /**
     * Removing accents from a string
     */
    protected function removeAccents(string $text): string
    {
        $accents = [
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
            'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
            'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
            'ç' => 'c', 'ñ' => 'n',
        ];

        return strtr($text, $accents);
    }

    /**
     * List of stop words - common words to ignore
     */
    protected function getStopWords(): array
    {
        return [
            'o', 'a', 'os', 'as', 'um', 'uma', 'uns', 'umas',
            'de', 'do', 'da', 'dos', 'das', 'em', 'no', 'na', 'nos', 'nas',
            'para', 'por', 'com', 'sem', 'sob', 'sobre',
            'que', 'qual', 'quais', 'quando', 'onde', 'como',
            'e', 'ou', 'mas', 'se', 'não', 'mais', 'muito', 'pouco',
            'ser', 'estar', 'ter', 'haver', 'fazer', 'dizer', 'ir', 'vir',
            'este', 'esta', 'estes', 'estas', 'esse', 'essa', 'esses', 'essas',
            'aquele', 'aquela', 'aqueles', 'aquelas',
            'sua', 'seu', 'suas', 'seus', 'nossa', 'nosso', 'nossas', 'nossos',
        ];
    }
}
