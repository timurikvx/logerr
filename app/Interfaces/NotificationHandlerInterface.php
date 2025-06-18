<?php

namespace App\Interfaces;

use App\Interfaces\Models\NotificationInterface;

interface NotificationHandlerInterface
{

    function addListener(NotificationEventHandlerInterface $handler): void;

    function handle(NotificationInterface $notification): bool;

}
