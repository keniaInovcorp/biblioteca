<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Event;
use App\Events\SubmissionCreated;
use App\Listeners\SendSubmissionCreatedNotifications;
use App\Models\User;
use App\Models\Submission;
use App\Observers\SubmissionObserver;
use App\Policies\AdminPolicy;
use App\Policies\SubmissionPolicy;

class AppServiceProvider extends ServiceProvider
{

    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => AdminPolicy::class,
        Submission::class => SubmissionPolicy::class,
    ];

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
        Gate::policy(User::class, AdminPolicy::class);
        Gate::policy(Submission::class, SubmissionPolicy::class);

        Event::listen(
            SubmissionCreated::class,
            SendSubmissionCreatedNotifications::class
        );

        Submission::observe(SubmissionObserver::class);
    }
}
