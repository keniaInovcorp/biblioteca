<?php

namespace App\Livewire;

use App\Models\Book;
use App\Models\Review;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Livewire component for displaying book reviews with pagination.
 * 
 * This component shows reviews in a collapsible section with
 * pagination support (5 reviews per page).
 */
class BookReviews extends Component
{
    use WithPagination;

    /**
     * The book whose reviews are being displayed.
     */
    #[Locked]
    public Book $book;

    /**
     * Whether the reviews section is expanded.
     */
    public bool $expanded = false;

    /**
     * Number of reviews per page.
     */
    protected int $perPage = 5;

    /**
     * Initialize the component with the book.
     *
     * @param Book $book The book to show reviews for
     */
    public function mount(Book $book): void
    {
        $this->book = $book;
    }

    /**
     * Toggle the expanded state of the reviews section.
     */
    public function toggleExpanded(): void
    {
        $this->expanded = !$this->expanded;
    }

    /**
     * Get the paginated reviews.
     */
    public function getReviewsProperty(): LengthAwarePaginator
    {
        return $this->book->activeReviews()
            ->with('user')
            ->latest()
            ->paginate($this->perPage);
    }

    /**
     * Get the total count of active reviews.
     */
    public function getReviewsCountProperty(): int
    {
        return $this->book->activeReviews()->count();
    }

    /**
     * Get the average rating.
     */
    public function getAverageRatingProperty(): float
    {
        return round($this->book->activeReviews()->avg('rating') ?? 0, 1);
    }

    /**
     * Check if user can access the review form.
     * User can access if:
     * - They can create a new review (canReviewBook), OR
     * - They already have a review (pending or rejected) for this book
     */
    public function getCanAccessReviewFormProperty(): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }

        // Check if user can create a new review
        if ($user->can('canReviewBook', $this->book->id)) {
            return true;
        }

        // Check if user has an existing review (pending or rejected)
        return Review::where('user_id', $user->id)
            ->where('book_id', $this->book->id)
            ->whereIn('status', ['pending', 'rejected'])
            ->exists();
    }

    /**
     * Render the component view.
     */
    public function render(): View
    {
        return view('livewire.book-reviews', [
            'reviews' => $this->reviews,
            'reviewsCount' => $this->reviewsCount,
            'averageRating' => $this->averageRating,
            'canAccessReviewForm' => $this->canAccessReviewForm,
        ]);
    }
}


