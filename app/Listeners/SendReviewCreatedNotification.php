<?php

namespace App\Listeners;

use App\Events\ReviewCreated;
use App\Models\User;
use App\Notifications\ReviewCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendReviewCreatedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(ReviewCreated $event): void
    {
        // Searching for admins using permission verification
        $admins = User::all()->filter(function ($user) {
            return $user->can('create', \App\Models\Book::class);
        });

        foreach ($admins as $index => $admin) {
            // Add 1 second delay between each email Mailtrap rate limiting
            if ($index > 0) {
                sleep(1);
            }
            $admin->notify(new ReviewCreatedNotification($event->review));
        }
    }
}
