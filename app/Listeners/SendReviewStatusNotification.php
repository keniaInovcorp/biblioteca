<?php

namespace App\Listeners;

use App\Events\ReviewStatusChanged;
use App\Notifications\ReviewStatusChangedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
         $event->review->user->notify(
            new ReviewStatusChangedNotification($event->review)
        );
    }
}
