<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationsFields extends Model
{
    use HasFactory;

    public $timestamps = false;
    public $incrementing = false;

    public static function getByOption($option): Collection
    {
        return self::query()->where('option', '=', $option)->get();
    }

}
