<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;

class LogOption extends Model
{
    use HasFactory;

    public static function set($team, $name, $value): string
    {
        $user = Auth::id();
        $option = self::query()
            ->where('user', '=', $user)
            ->where('team', '=', $team)
            ->where('name', '=', $name)
            ->first();

        if(is_null($option)){
            $option = new LogOption();
            $option->user = $user;
            $option->team = $team;
            $option->name = $name;
            $option->guid = Uuid::uuid4()->toString();
        }
        $option->data = json_encode($value);
        $option->save();
        return $option->guid;
    }

    public static function setByGuid($team, $guid, $value): string|null
    {
        $user = Auth::id();
        $option = self::query()
            ->where('user', '=', $user)
            ->where('team', '=', $team)
            ->where('guid', '=', $guid)
            ->first();

        if(is_null($option)){
            return null;
        }
        $option->data = json_encode($value);
        $option->save();
        return $option->guid;
    }

    public static function getByGuid($team, $guid, $without_data = false): mixed
    {
        $user = Auth::id();
        $option = self::query()
            ->where('user', '=', $user)
            ->where('team', '=', $team)
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

    public static function getAll($team, $without_data = false): array
    {
        $user = Auth::id();
        $options = self::query()
            ->select(['name', 'guid', 'data'])
            ->where('user', '=', $user)
            ->where('team', '=', $team)
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

    public static function remove($team, $name): void
    {
        $user = Auth::id();
        $option = self::query()
            ->where('user', '=', $user)
            ->where('team', '=', $team)
            ->where('name', '=', $name)
            ->first();
        if(!is_null($option)){
            $option->delete();
        }
    }

    public static function removeByGuid($team, $guid): void
    {
        $user = Auth::id();
        $option = self::query()
            ->where('user', '=', $user)
            ->where('team', '=', $team)
            ->where('guid', '=', $guid)
            ->first();
        if(!is_null($option)){
            $option->delete();
        }
    }

    public static function clearData(&$options): void
    {
        foreach ($options as $option){
            unset($option['data']);
        }
    }

}
