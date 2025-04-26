<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationsOption extends Model
{
    use HasFactory;

    public static function getOptions($team, $type): Collection
    {
        $query = self::query()
            ->where('team', '=', $team)
            ->where('type', '=', $type);
        return $query->get();
    }

    public static function getByGuid($team, $guid): mixed
    {
        $query = self::query()
            ->where('team', '=', $team)
            ->where('guid', '=', $guid);
        return $query->first();
    }

    public static function getByTeam($team): Collection
    {
        $query = self::query()->where('team', '=', $team);
        return $query->get();
    }

    public static function notification(): void
    {
        $team = 2; //intval(UserOption::get('current_team'));
        $notifications = self::getByTeam($team);
        $now = now()->modify('+3 hours');
        $list = collect([]);
        foreach ($notifications as $notification){

            //dump($notification->toArray());
            $fields = NotificationsFields::getByOption($notification->id);
            $start = now()->modify('+3 hours')->modify('-'.$notification->minutes.' minutes');
            $amount = $notification->count;
            foreach ($fields as $field){

                $count = Error::query()->where($field->field, '=', $field->value)
                    ->where('team', '=', $team)
                    ->whereBetween('date', [$start->format('Y-m-d H:i:s'), $now->format('Y-m-d H:i:s')])
                    ->count();
                //dump('ddd');
                //dump($start->format('Y-m-d H:i:s').' '.$now->format('Y-m-d H:i:s'));
//                dump($field->field.' = '.$field->value);
//                dump($count);
//                dump($amount);
                if($count > $amount){
                    $text = 'За последние '.$notification->minutes.' минут '.$field->value.' найдено '.$count.' ошибок';
                    dump($text);
                }
//                if($count < $amount){
//                    continue;
//                }
                $list->push([

                ]);
            }



        }
    }
}
