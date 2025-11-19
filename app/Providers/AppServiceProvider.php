<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Event;
use App\Events\SubmissionCreated;
use App\Events\ReviewCreated;
use App\Events\ReviewStatusChanged;
use App\Listeners\SendSubmissionCreatedNotifications;
use App\Listeners\SendReviewCreatedNotification;
use App\Listeners\SendReviewStatusNotification;
use App\Models\User;
use App\Models\Submission;
use App\Models\Review;
use App\Observers\SubmissionObserver;
use App\Policies\AdminPolicy;
use App\Policies\SubmissionPolicy;
use App\Policies\ReviewPolicy;
use Illuminate\Console\Scheduling\Schedule;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register policies
        Gate::policy(User::class, AdminPolicy::class);
        Gate::policy(Submission::class, SubmissionPolicy::class);
        Gate::policy(Review::class, ReviewPolicy::class);

        Gate::define('canReviewBook', function (User $user, $bookId) {
            $policy = new ReviewPolicy();
            return $policy->canReviewBook($user, $bookId);
        });

        Event::listen(
            SubmissionCreated::class,
            SendSubmissionCreatedNotifications::class
        );

        Event::listen(
            ReviewCreated::class,
            SendReviewCreatedNotification::class
        );

        Event::listen(
            ReviewStatusChanged::class,
            SendReviewStatusNotification::class
        );

        Submission::observe(SubmissionObserver::class);

        // Schedule send return reminders daily at 12:38
        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            $schedule->command('reminders:due-returns')->dailyAt('12:38');
        });
    }
}
