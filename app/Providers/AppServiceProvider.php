<?php

namespace App\Providers;

use App\Events\HandleErrorsEvent;
use App\Events\HandleLogsEvent;
use App\Listeners\HandleErrorsListener;
use App\Listeners\HandleLogsListener;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

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
        Event::listen(HandleErrorsEvent::class, HandleErrorsListener::class);
        Event::listen(HandleLogsEvent::class, HandleLogsListener::class);
    }
}
