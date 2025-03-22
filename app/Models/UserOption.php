<?php

namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class UserOption extends Model
{
    use HasFactory;

    public static function set($name, $team, $value): void
    {
        $user = Auth::id();
        $option = self::query()
            ->where('user', '=', $user)
            ->where('team', '=', $team)
            ->where('name', '=', $name)
            ->first();
        if(is_null($option)){
            $option = new UserOption();
            $option->user = $user;
            $option->team = $team;
            $option->name = $name;
        }
        $option->data = json_encode($value);
        $option->save();
        Cache::set('user_option'.$user.$team.$name, json_encode($value), 3600);
    }

    public static function get($name, $team, $default = null): array|string
    {
        $user = Auth::id();
        $data = Cache::get('user_option'.$user.$team.$name);
        if(!is_null($data)){
            return json_decode($data, true);
        }
        $option = self::query()
            ->where('user', '=', $user)
            ->where('team', '=', $team)
            ->where('name', '=', $name)
            ->first();
        if(is_null($option)){
            return $default;
        }
        return json_decode($option->data, true);
    }

}
