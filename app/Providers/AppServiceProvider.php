<?php

namespace App\Providers;

use App\Domain\Complaint\Events\ComplaintCreated;
use App\Domain\Complaint\Listeners\SendComplaintCreatedNotification;
use App\Domain\Complaint\Models\Complaint;
use App\Domain\Complaint\Policies\ComplaintPolicy;
use App\Infrastructure\Persistence\Repositories\ComplaintRepository;
use App\Infrastructure\Persistence\Repositories\WorkflowRepository;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ComplaintRepository::class);
        $this->app->singleton(WorkflowRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        Event::listen(ComplaintCreated::class, SendComplaintCreatedNotification::class);

        Gate::policy(Complaint::class, ComplaintPolicy::class);
    }
}
