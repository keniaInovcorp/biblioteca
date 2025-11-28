<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;
use App\Models\Submission;
use Illuminate\Auth\Access\Response;

class ReviewPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Review $review): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     * Only citizens can create reviews.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('citizen');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Review $review): bool
    {
        //Only the reviewer can edit if the review is pending.
        return $user->id === $review->user_id && $review->isPending();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Review $review): bool
    {
        // Only the user can delete it if it is pending.
        return $user->id === $review->user_id && $review->isPending();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Review $review): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Review $review): bool
    {
        return false;
    }

    /**
     * Determine whether the user can moderate any reviews.
     */
    public function moderateAny(User $user): bool
    {
        // Only admins can moderate reviews
        return $user->can('create', \App\Models\Book::class);
    }

    /**
     * Determine whether the user can moderate a specific review.
     */
    public function moderate(User $user, Review $review): bool
    {
        // Only admins can moderate reviews
        return $user->can('create', \App\Models\Book::class);
    }

    /**
     * Checks if the user can review a specific book.
     * Only citizens who have returned the book can create reviews.
     * User can create new review ONLY if:
     * - Never reviewed before (no review exists)
     */
    public function canReviewBook(User $user, $bookId): bool
    {
        // Only citizens can create reviews
        if (!$user->hasRole('citizen')) {
            return false;
        }

        // Check if you have a returned submission for this book.
        $hasReturnedBook = Submission::where('user_id', $user->id)
            ->where('book_id', $bookId)
            ->where('status', 'returned')
            ->exists();

        if (!$hasReturnedBook) {
            return false;
        }

        // Check if user already has a review for this book
        $existingReview = Review::where('user_id', $user->id)
            ->where('book_id', $bookId)
            ->exists();

        // Can only create if no review exists
        return !$existingReview;
    }

    /**
     * Check if user has a pending review for a book.
     */
    public function hasPendingReview(User $user, $bookId): bool
    {
        return Review::where('user_id', $user->id)
            ->where('book_id', $bookId)
            ->where('status', 'pending')
            ->exists();
    }

    /**
     * Get user's current review for a book (pending or rejected, not approved).
     */
    public function getCurrentReview(User $user, $bookId): ?Review
    {
        return Review::where('user_id', $user->id)
            ->where('book_id', $bookId)
            ->whereIn('status', ['pending', 'rejected'])
            ->latest()
            ->first();
    }
}
