<?php

namespace App\Interfaces;

use App\Interfaces\Models\NotificationInterface;
use App\Services\DTO\NotificationDTO;
use Illuminate\Support\Collection;

interface NotificationProviderInterface
{

    function get(): Collection;

    function handle(NotificationInterface $notification): bool;

    function miss(NotificationInterface $notification): bool;

    function complete(NotificationInterface $notification): bool;

    function getByGuid(string $guid): NotificationInterface|null;

    function create(NotificationDTO $notification): void;

}
