<?php

namespace App\Interfaces;

use App\Interfaces\Models\TeamInterface;
use App\Interfaces\Models\UserNotificationInterface;
use Illuminate\Database\Eloquent\Collection;

interface UserNotificationProviderInterface
{
    function create(TeamInterface $team, string $guid, string $type, string $name, string $chat, int $minutes, int $count, int $every): UserNotificationInterface;

    function update(TeamInterface $team, string $guid, string $name, string $chat, int $minutes, int $count, int $every): UserNotificationInterface;

    function getByGuid(TeamInterface $team, string $guid): UserNotificationInterface|null;

    function clearFields($notification): void;

    function writeField($notification, string $field, $value): void;

    function list(TeamInterface $team, string $type): Collection;

}
