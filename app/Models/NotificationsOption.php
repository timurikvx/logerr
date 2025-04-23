<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationsOption extends Model
{
    use HasFactory;

    public static function getOptions($team, $type)
    {
        $query = self::query()
            ->where('team', '=', $team)
            ->where('type', '=', $type);
        return $query->get();
    }

    public static function getByGuid($team, $guid)
    {
        $query = self::query()
            ->where('team', '=', $team)
            ->where('guid', '=', $guid);
        return $query->first();
    }

}
