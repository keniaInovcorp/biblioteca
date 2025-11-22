<?php

namespace App\Listeners;

use App\Events\ReviewStatusChanged;
use App\Notifications\ReviewStatusChangedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;

class SendReviewStatusNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ReviewStatusChanged $event): void
    {
        $review = $event->review;

        // Prevent duplicate notifications using cache
        // Use review ID and status to ensure we only send one notification per status change
        $cacheKey = "review_status_notification_sent_{$review->id}_{$review->status}";

        // Try to acquire a lock for 5 mints/300 seconds
        $lock = Cache::lock($cacheKey . '_lock', 300);

        if (!$lock->get()) {
            return;
        }

        try {
            // Check if notification was already sent
            if (Cache::has($cacheKey)) {
                return;
            }

            Cache::put($cacheKey, true, now()->addMinutes(10));

            // Please refresh the page to ensure you have the most up-to-date data.
            $review->refresh();

            $review->user->notify(
                new ReviewStatusChangedNotification($review)
            );
        } finally {
            $lock->release();
        }
    }
}
