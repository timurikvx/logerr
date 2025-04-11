<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Ramsey\Uuid\Uuid;

class Option extends Model
{
    use HasFactory;

    public $incrementing = false;
    public $timestamps = false;

    public static function set($team, $name, $value, $category = null): string
    {
        $user = Auth::id();
        $option = self::query()
            ->where('user', '=', $user)
            ->where('team', '=', $team)
            ->where('name', '=', $name)
            ->where('category', '=', $category)
            ->delete();

        if(is_null($option)){
            $option = new Option();
            $option->user = $user;
            $option->team = $team;
            $option->name = $name;
            $option->category = $category;
            $option->guid = Uuid::uuid4()->toString();
        }
        $option->data = json_encode($value);
        $option->save();
        return $option->guid;
    }

    public static function setByGuid($team, $guid, $value, $category = null): string|null
    {
        $user = Auth::id();
        $option = self::query()
            ->where('user', '=', $user)
            ->where('team', '=', $team)
            ->where('category', '=', $category)
            ->where('guid', '=', $guid)
            ->first();

        if(is_null($option)){
            return null;
        }
        $option->data = json_encode($value);
        $option->save();
        return $option->guid;
    }

    public static function getByGuid($team, $guid, $category = null, $without_data = false): mixed
    {
        $user = Auth::id();
        $option = self::query()
            ->where('user', '=', $user)
            ->where('team', '=', $team)
            ->where('category', '=', $category)
            ->where('guid', '=', $guid)
            ->first();
        if(is_null($option)){
            return null;
        }
        $item = ['name'=>$option->name, 'guid'=> $option->guid];
        if(!$without_data){
            $item['data'] = json_decode($option->data, true);
        }
        return $item;
    }

    public static function updateByGuid($team, $guid, $value, $category = null): string|null
    {
        $user = Auth::id();
        $option = self::query()
            ->where('user', '=', $user)
            ->where('team', '=', $team)
            ->where('category', '=', $category)
            ->where('guid', '=', $guid)
            ->first();
        if(is_null($option)){
            return null;
        }
        $option->data = json_encode($value);
        $option->save();
        //Cache::set('error_option'.$user.'_'.$option->guid, json_encode($option->toArray()), 3600);
        return $option->guid;
    }

    public static function getAll($team, $category, $without_data = false): array
    {
        $user = Auth::id();
        $options = self::query()
            ->select(['name', 'guid', 'data'])
            ->where('user', '=', $user)
            ->where('team', '=', $team)
            ->where('category', '=', $category)
            ->get();
        $list = [];
        foreach ($options as $option){
            $item = ['name'=>$option->name, 'guid'=> $option->guid];
            if(!$without_data){
                $item['data'] = json_decode($option->data, true);
            }
            $list[] = $item;
        }
        return $list;
    }

    public static function remove($team, $name, $category = null): void
    {
        $user = Auth::id();
        self::query()
            ->where('user', '=', $user)
            ->where('team', '=', $team)
            ->where('name', '=', $name)
            ->where('category', '=', $category)
            ->delete();
    }

    public static function removeByGuid($team, $guid): void
    {
        $user = Auth::id();
        self::query()
            ->where('user', '=', $user)
            ->where('team', '=', $team)
            ->where('guid', '=', $guid)
            ->delete();
    }

    public static function clearData(&$options): void
    {
        foreach ($options as $option){
            unset($option['data']);
        }
    }
}
