<?php

namespace App\Services;

use App\Interfaces\NotificationInterface;
use App\Models\Crew;
use App\Models\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class NotificationProvider implements NotificationInterface
{

    public function get(): Collection
    {
        return Notification::get();
    }

    public function complete($guid): bool
    {
        $notification = Notification::getByGuid($guid);
        if(is_null($notification)){
            return false;
        }
        $notification->completed = true;
        $notification->save();
        return true;
    }

}
