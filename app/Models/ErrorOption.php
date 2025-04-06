<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Ramsey\Uuid\Uuid;

class ErrorOption extends Model
{
    use HasFactory;

    public static function set($team, $name, $value): string
    {
        $user = Auth::id();
        $query = self::query()
            ->where('user', '=', $user)
            ->where('team', '=', $team)
            ->where('name', '=', $name);
        $option = $query->first();

        if(is_null($option)){
            $option = new ErrorOption();
            $option->user = $user;
            $option->team = $team;
            $option->name = $name;
            $option->guid = Uuid::uuid4()->toString();
        }
        $option->data = json_encode($value);
        $option->save();
        Cache::set('error_option'.$user.'_'.$option->guid, json_encode($option->toArray()), 3600);
        Cache::delete('all_error_option'.$user.'_'.$team);
        return $option->guid;
    }

    public static function updateByGuid($team, $guid, $value): string|null
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
        Cache::set('error_option'.$user.'_'.$option->guid, json_encode($option->toArray()), 3600);
        return $option->guid;
    }

    public static function getByGuid($team, $guid, $without_data = false): mixed
    {
        $user = Auth::id();
        $data = Cache::get('error_option'.$user.'_'.$guid);
        if(!is_null($data)){
            $option = json_decode($data);
            $item = ['name'=>$option->name, 'guid'=> $option->guid];
            if(!$without_data){
                $item['data'] = json_decode($option->data, true);
            }
            return $item;
        }
        $option = self::query()
            ->where('user', '=', $user)
            ->where('team', '=', $team)
            ->where('guid', '=', $guid)
            ->first();
        if(is_null($option)){
            return null;
        }
        Cache::set('error_option'.$user.'_'.$option->guid, json_encode($option->toArray()), 3600);
        $item = ['name'=>$option->name, 'guid'=> $option->guid];
        if(!$without_data){
            $item['data'] = json_decode($option->data, true);
        }
        return $item;
    }

    public static function getAll($team, $without_data = false): array
    {
        $user = Auth::id();
        $data = Cache::get('all_error_option'.$user.'_'.$team);
        if(!is_null($data)){
            $options = json_decode($data);
        }else{
            $options = self::query()->select(['name', 'guid', 'data'])
                ->where('user', '=', $user)
                ->where('team', '=', $team)->get();
            if($options->count() > 0){
                Cache::set('all_error_option'.$user.'_'.$team, json_encode($options->toArray()), 3600);
            }
        }
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
            Cache::delete('error_option'.$user.'_'.$option->guid);
            Cache::delete('all_error_option'.$user.'_'.$team);
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
            Cache::delete('error_option'.$user.'_'.$option->guid);
            Cache::delete('all_error_option'.$user.'_'.$team);
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
