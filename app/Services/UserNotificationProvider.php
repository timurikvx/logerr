<?php

namespace App\Services;

use App\Interfaces\Models\TeamInterface;
use App\Interfaces\Models\UserNotificationInterface;
use App\Interfaces\UserNotificationProviderInterface;
use App\Models\NotificationsFields;
use App\Models\NotificationsOption;
use Illuminate\Database\Eloquent\Collection;

class UserNotificationProvider implements UserNotificationProviderInterface
{

    public function create(TeamInterface $team, string $guid, string $type, string $name, string $chat, int $minutes, int $count, int $every): UserNotificationInterface
    {
        $notification = new NotificationsOption();
        $notification->team = $team->getID();
        $notification->guid = $guid;
        $notification->type = $type;
        $notification->name = $name;
        $notification->chat = $chat;
        $notification->minutes = max(0, intval($minutes));
        $notification->count = max(0, intval($count));
        $notification->every = max(0, intval($every));
        //dump($notification);
        $notification->save();
        return $notification;
    }

    public function update(TeamInterface $team, string $guid, string $name, string $chat, int $minutes, int $count, int $every): UserNotificationInterface
    {
        $notification = $this->getByGuid($team, $guid);
        $notification->name = $name;
        $notification->chat = $chat;
        $notification->minutes = max(0, intval($minutes));
        $notification->count = max(0, intval($count));
        $notification->every = max(0, intval($every));
        $notification->save();
        return $notification;
    }

    public function getByGuid(TeamInterface $team, string $guid): UserNotificationInterface|null
    {
        return NotificationsOption::getByGuid($team->getID(), $guid);
    }

    public function clearFields($notification): void
    {
        NotificationsFields::query()->where('option', '=', $notification->id)->delete();
    }

    public function writeField($notification, string $field, $value): void
    {
        $option_field = new NotificationsFields();
        $option_field->option = $notification->id;
        $option_field->field = $field;
        $option_field->value = $value;
        $option_field->save();
    }

    public function list(TeamInterface $team, string $type): Collection
    {
        return NotificationsOption::getOptions($team->getID(), $type);
    }


}
