<?php

namespace App\Events;

use App\Models\Submission;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubmissionCreated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public Submission $submission)
    {
        $this->submission->loadMissing(['book', 'user']);
    }
}
