<?php

namespace App\Mail;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Mime\Email;

class SubmissionCreatedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(public Submission $submission)
    {
        $this->submission->loadMissing(['book', 'user']);
    }

    public function build(): self
    {
        $book = $this->submission->book;
        $coverUrl = null;
        $coverCid = null;

        if ($book?->cover_image_url) {
            $relative = ltrim(preg_replace('#^/?storage/#', '', $book->cover_image_url), '/');

            if ($relative && Storage::disk('public')->exists($relative)) {
                $absolutePath = Storage::disk('public')->path($relative);

                $this->withSymfonyMessage(function (Email $message) use ($absolutePath) {
                    $message->embedFromPath($absolutePath, 'book-cover');
                });

                $coverCid = 'book-cover';
            } else {
                $coverUrl = url($book->cover_image_url);
            }
        }

        return $this->subject(__('Requisição criada'))
            ->markdown('emails.submissions.created', [
                'submission' => $this->submission,
                'book' => $book,
                'user' => $this->submission->user,
                'coverUrl' => $coverUrl,
                'coverCid' => $coverCid,
            ]);
    }
}
