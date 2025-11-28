<?php

namespace App\Listeners;

use App\Events\ReviewCreated;
use App\Models\User;
use App\Notifications\ReviewCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;

class SendReviewCreatedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Number of times the job may be attempted.
     */
    public int $tries = 1;

    /**
     * Determine if the listener should be queued.
     */
    public function shouldQueue(ReviewCreated $event): bool
    {
        $review = $event->review;
        $cacheKey = "review_notification_queued_{$review->id}";

        // Prevent duplicate queue jobs
        if (Cache::has($cacheKey)) {
            return false;
        }

        Cache::put($cacheKey, true, now()->addMinutes(10));
        return true;
    }

    /**
     * Handle the event.
     */
    public function handle(ReviewCreated $event): void
    {
        $review = $event->review;

        // Double-check to prevent duplicate notifications
        $cacheKey = "review_notification_sent_{$review->id}";

        if (Cache::has($cacheKey)) {
            return;
        }

        // Mark as sent
        Cache::put($cacheKey, true, now()->addHours(24));

        // Get admins who can manage books
        $admins = User::all()->filter(function ($user) {
            return $user->can('create', \App\Models\Book::class);
        });

        foreach ($admins as $index => $admin) {
            // Add delay between emails to avoid rate limiting
            if ($index > 0) {
                sleep(1);
            }
            $admin->notify(new ReviewCreatedNotification($review));
        }
    }
}
