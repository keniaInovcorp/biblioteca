<?php

namespace App\Observers;

use App\Events\SubmissionCreated;
use App\Models\Submission;

class SubmissionObserver
{
    public function created(Submission $submission): void
    {
        SubmissionCreated::dispatch($submission);
    }
}


