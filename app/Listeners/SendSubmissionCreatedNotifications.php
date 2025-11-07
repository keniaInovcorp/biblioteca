<?php

namespace App\Listeners;

use App\Events\SubmissionCreated;
use App\Mail\SubmissionCreatedMail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;

class SendSubmissionCreatedNotifications implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(SubmissionCreated $event): void
    {
        $submission = $event->submission->loadMissing(['book', 'user']);
        $user = $submission->user;

        // Debounce duplicate dispatches for the same submission
        $onceKey = 'submission-mail-sent:' . $submission->id;
        if (! Cache::add($onceKey, true, now()->addMinutes(2))) {
            return;
        }

        $adminEmails = User::role('admin')
            ->pluck('email')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $primary = $user?->email ?? array_shift($adminEmails);

        if (! $primary) {
            return;
        }

        $message = Mail::to($primary);

        if ($user?->email) {
            $adminEmails = array_filter($adminEmails, fn ($email) => $email !== $user->email);
        }

        if (! empty($adminEmails)) {
            $message->bcc($adminEmails);
        }

        $delaySeconds = 5; // base delay to respect Mailtrap free tier
        $lock = Cache::lock('mailtrap-send-lock', 1);
        if (! $lock->get()) {
            $delaySeconds = 8;
        }

        $when = now()->addSeconds($delaySeconds);
        $message->later($when, new SubmissionCreatedMail($submission));

        if ($lock->owner()) {
            $lock->release();
        }
    }
}
