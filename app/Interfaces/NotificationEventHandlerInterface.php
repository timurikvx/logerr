<?php

namespace App\Interfaces;

use App\Interfaces\Models\NotificationInterface;

interface NotificationEventHandlerInterface
{
    function allEvents(): array;

    function handleEvent(NotificationInterface $notification): bool;

}
