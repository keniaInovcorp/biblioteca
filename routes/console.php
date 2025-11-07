<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use App\Models\Submission;
// use App\Models\User;
use App\Mail\SubmissionReminderMail;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('reminders:due-returns', function () {
    $count = 0;
    Submission::with(['user', 'book'])
        ->where('status', 'created')
        ->whereNull('reminder_sent_at')
        ->whereDate('expected_return_date', now()->addDay()->startOfDay())
        ->orderBy('id')
        ->chunkById(100, function ($submissions) use (&$count) {
            foreach ($submissions as $submission) {
                $user = $submission->user;
                if (! $user || empty($user->email)) {
                    continue;
                }

                Mail::to($user->email)->queue(new SubmissionReminderMail($submission));
                $submission->forceFill(['reminder_sent_at' => now()])->save();
                $count++;
            }
        });

    $this->info("Reminders queued: {$count}");
})->purpose('Send email reminders for returns due tomorrow');
