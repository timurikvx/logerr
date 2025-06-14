<?php

namespace App\Providers;

use App\Events\HandleErrorsEvent;
use App\Events\HandleLogsEvent;
use App\Interfaces\FilterProviderInterface;
use App\Interfaces\IColumnService;
use App\Interfaces\IListSettings;
use App\Interfaces\IListSettingsService;
use App\Interfaces\IListPreferences;
use App\Interfaces\IListProvider;
use App\Interfaces\ILogerrCache;
use App\Interfaces\ITeamProvider;
use App\Interfaces\ITeamService;
use App\Interfaces\IUserProvider;
use App\Interfaces\IUserSettingsService;
use App\Interfaces\Models\UserInterface;
use App\Interfaces\NotificationInterface;
use App\Interfaces\TelegramChatProviderInterface;
use App\Interfaces\UserNotificationProviderInterface;
use App\Listeners\HandleErrorsListener;
use App\Listeners\HandleLogsListener;
use App\Models\User;
use App\Services\Cache\CacheService;
use App\Services\FilterProvider;
use App\Services\ListOptions\ListSettingsService;
use App\Services\ListProvider;
use App\Services\ListSettings;
use App\Services\NotificationProvider;
use App\Services\TeamProvider;
use App\Services\Teams\TeamService;
use App\Services\TelegramChatProvider;
use App\Services\UserNotificationProvider;
use App\Services\UserOptions\UserSettingsService;
use App\Services\UserProvider;
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
        $this->app->bind(ITeamProvider::class, TeamProvider::class);
        $this->app->bind(IUserProvider::class, UserProvider::class);
        $this->app->bind(UserInterface::class, User::class);
        $this->app->bind(FilterProviderInterface::class, FilterProvider::class);
        $this->app->bind(NotificationInterface::class, NotificationProvider::class);
        $this->app->bind(TelegramChatProviderInterface::class, TelegramChatProvider::class);
        $this->app->bind(UserNotificationProviderInterface::class, UserNotificationProvider::class);

    }
}
