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
     * Handle the event.
     */
    public function handle(ReviewCreated $event): void
    {
        $review = $event->review;

        // Prevent duplicate notifications using cache
        // Use review ID and created_at timestamp to ensure uniqueness
        $cacheKey = "review_notification_sent_{$review->id}_{$review->created_at->timestamp}";

        // Check if notification was already sent within last 10 mints
        if (Cache::has($cacheKey)) {
            return;
        }

        // Mark as sent for 10 minutes
        Cache::put($cacheKey, true, now()->addMinutes(10));

        // Searching for admins using permission verification
        $admins = User::all()->filter(function ($user) {
            return $user->can('create', \App\Models\Book::class);
        });

        foreach ($admins as $index => $admin) {
            // Add 1 second delay between each email Mailtrap rate limiting
            if ($index > 0) {
                sleep(1);
            }
            $admin->notify(new ReviewCreatedNotification($review));
        }
    }
}
