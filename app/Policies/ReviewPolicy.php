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
     */
    public function create(User $user): bool
    {
        // Só cidadãos criam reviews
        return !$user->can('create', \App\Models\Book::class);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Review $review): bool
    {
        // Apenas o user do review pode editar se estiver pendente
        return $user->id === $review->user_id && $review->isPending();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Review $review): bool
    {
        // Só user pode deletar se estiver pendente
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

    public function moderate(User $user, Review $review): bool
    {
        //Only admins moderated.
        return $user->can('create', \App\Models\Book::class);
    }

    /**
     * Checks if the user can review a specific book.
     */
    public function canReviewBook(User $user, $bookId): bool
    {
        // See if you've already done a review.
        if (Review::where('user_id', $user->id)
            ->where('book_id', $bookId)
            ->exists()) {
            return false;
        }

        // Check if you have a submitted submission for this book.
        return Submission::where('user_id', $user->id)
            ->where('book_id', $bookId)
            ->where('status', 'returned')
            ->exists();
    }
}
