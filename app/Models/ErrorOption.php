<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;

class ErrorOption extends Model
{
    use HasFactory;

    public static function set($name, $value): void
    {
        $user = Auth::id();
        $option = self::query()->where('user', '=', $user)->where('name', '=', $name)->first();
        if(is_null($option)){
            $option = new ErrorOption();
            $option->user = $user;
            $option->name = $name;
            $option->guid = Uuid::uuid4()->toString();
        }
        $option->data = json_encode($value);
        $option->save();
    }

    public static function get($name, $default = null): array
    {
        $user = Auth::id();
        $option = self::query()->where('user', '=', $user)->where('name', '=', $name)->first();
        if(is_null($option)){
            return $default;
        }
        return ['name'=>$option->name, 'guid'=> $option->guid, 'value'=>json_decode($option->data, true)];
    }

    public static function getAll($without_data = false): array
    {
        $user = Auth::id();
        $options = self::query()->select(['name', 'guid', 'data'])->where('user', '=', $user)->get();
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

    public static function remove($name): void
    {
        $user = Auth::id();
        $option = self::query()->where('user', '=', $user)->where('name', '=', $name)->first();
        if(!is_null($option)){
            $option->delete();
        }
    }


}
