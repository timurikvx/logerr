<?php

namespace App\Providers;

use App\Events\HandleErrorsEvent;
use App\Events\HandleLogsEvent;
use App\Interfaces\IColumnService;
use App\Interfaces\IListSettings;
use App\Interfaces\IListSettingsService;
use App\Interfaces\IListPreferences;
use App\Interfaces\IListProvider;
use App\Interfaces\ILogerrCache;
use App\Interfaces\ITeamProvider;
use App\Interfaces\ITeamService;
use App\Interfaces\IUserSettingsService;
use App\Listeners\HandleErrorsListener;
use App\Listeners\HandleLogsListener;
use App\Services\Cache\CacheService;
use App\Services\ListOptions\ListSettingsService;
use App\Services\ListProvider;
use App\Services\ListSettings;
use App\Services\TeamProvider;
use App\Services\Teams\TeamService;
use App\Services\UserOptions\UserSettingsService;
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

        $this->app->bind(ILogerrCache::class, CacheService::class);
        $this->app->bind(ITeamService::class, TeamService::class);
        $this->app->bind(IUserSettingsService::class, UserSettingsService::class);
        $this->app->bind(IListSettingsService::class, ListSettingsService::class);
        $this->app->bind(IListProvider::class, ListProvider::class);
        $this->app->bind(IListSettings::class, ListSettings::class);
        //$this->app->bind(IListPreferences::class, ListPreferences::class);
        $this->app->bind(ITeamProvider::class, TeamProvider::class);

    }
}
