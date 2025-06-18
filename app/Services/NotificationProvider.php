<?php

namespace App\Services;

use App\Interfaces\Models\NotificationInterface;
use App\Interfaces\NotificationHandlerInterface;
use App\Interfaces\NotificationProviderInterface;
use App\Models\Notification;
use App\Services\DTO\NotificationDTO;
use App\Services\Teams\TeamMemberProvider;
use Illuminate\Support\Collection;

class NotificationProvider implements NotificationProviderInterface
{

    public function __construct(private readonly NotificationHandlerInterface $notificationHandler)
    {
        //listeners
        $this->notificationHandler->addListener(new TeamMemberProvider());
    }

    public function get(): Collection
    {
        return Notification::get();
    }

    public function handle(NotificationInterface $notification): bool
    {
        $ok = $this->notificationHandler->handle($notification);
        if($ok){
            $notification->setCompleted(true);
            $notification->save();
        }
        return $ok;
    }

    public function miss(NotificationInterface $notification): bool
    {
        $notification->setMissed(true);
        return $notification->save();
    }

    public function complete(NotificationInterface $notification): bool
    {
        $notification->setCompleted(true);
        return $notification->save();
    }

    public function getByGuid(string $guid): NotificationInterface|null
    {
        $notification = Notification::getByGuid($guid);
        if($notification == null){
            return null;
        }
        return $notification;
    }

    public function create(NotificationDTO $notification): void
    {
        Notification::create($notification->type, $notification->to, $notification->title, $notification->text, $notification->data, $notification->url);
    }

}
