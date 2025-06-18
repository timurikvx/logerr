<?php

namespace App\Services\Notifications;

use App\Interfaces\Models\NotificationInterface;
use App\Interfaces\NotificationEventHandlerInterface;
use App\Interfaces\NotificationHandlerInterface;

class NotificationHandler implements NotificationHandlerInterface
{

    private array $handlers;

    public function __construct()
    {
        $this->handlers = [];
    }

    public function addListener(NotificationEventHandlerInterface $handler): void
    {
        $this->handlers[] = $handler;
    }

    public function handle(NotificationInterface $notification): bool
    {
        $handler = $this->getHandler($notification->getType());
        if(is_null($handler)){
            return false;
        }
        return $handler->handleEvent($notification);
    }

    private function getHandler(string $event): NotificationEventHandlerInterface|null
    {
        foreach ($this->handlers as $handler)
        {
            $events = $handler->allEvents();
            if(in_array($event, $events)){
                return $handler;
            }
        }
        return null;
    }

}
