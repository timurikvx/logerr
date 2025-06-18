<?php

namespace App\Providers;

use App\Events\HandleErrorsEvent;
use App\Events\HandleLogsEvent;
use App\Interfaces\FilterProviderInterface;
use App\Interfaces\ListSettingsInterface;
use App\Interfaces\ListSettingsServiceInterface;
use App\Interfaces\ListProviderInterface;
use App\Interfaces\ListWriterProviderInterface;
use App\Interfaces\LogerrCacheInterface;
use App\Interfaces\NotificationHandlerInterface;
use App\Interfaces\NotificationProviderInterface;
use App\Interfaces\QueueProviderInterface;
use App\Interfaces\TeamProviderInterface;
use App\Interfaces\Teams\TeamMembersInterface;
use App\Interfaces\TeamServiceInterface;
use App\Interfaces\UserProviderInterface;
use App\Interfaces\UserSettingsServiceInterface;
use App\Interfaces\Models\UserInterface;
use App\Interfaces\Models\NotificationInterface;
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
use App\Services\ListWriterProvider;
use App\Services\NotificationProvider;
use App\Services\Notifications\NotificationHandler;
use App\Services\QueueProvider;
use App\Services\TeamProvider;
use App\Services\Teams\TeamMemberProvider;
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

        $this->app->bind(LogerrCacheInterface::class, CacheService::class);
        $this->app->bind(TeamServiceInterface::class, TeamService::class);
        $this->app->bind(UserSettingsServiceInterface::class, UserSettingsService::class);
        $this->app->bind(ListSettingsServiceInterface::class, ListSettingsService::class);

        $this->app->bind(ListProviderInterface::class, ListProvider::class);
        $this->app->bind(ListSettingsInterface::class, ListSettings::class);
        $this->app->bind(TeamProviderInterface::class, TeamProvider::class);
        $this->app->bind(UserProviderInterface::class, UserProvider::class);
        $this->app->bind(UserInterface::class, User::class);
        $this->app->bind(FilterProviderInterface::class, FilterProvider::class);
        $this->app->bind(NotificationProviderInterface::class, NotificationProvider::class);
        $this->app->bind(TelegramChatProviderInterface::class, TelegramChatProvider::class);
        $this->app->bind(UserNotificationProviderInterface::class, UserNotificationProvider::class);
        $this->app->bind(ListWriterProviderInterface::class, ListWriterProvider::class);
        $this->app->bind(QueueProviderInterface::class, QueueProvider::class);
        $this->app->bind(NotificationHandlerInterface::class, NotificationHandler::class);
        $this->app->bind(TeamMembersInterface::class, TeamMemberProvider::class);

    }
}
