<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Submission;
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
    }
}
