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

    public static function notification($team): void
    {
        $notifications = self::getByTeam($team);
        $now = now()->modify('+3 hours');
        $all = collect([]);
        foreach ($notifications as $notification){

            $columns = self::getColumns($notification->type);
            $fields = NotificationsFields::getByOption($notification->id);
            $start = now()->modify('+3 hours')->modify('-'.$notification->minutes.' minutes');
            $amount = $notification->count;

            $query = Error::query()
                ->where('team', '=', $team)
                ->whereBetween('date', [$start->format('Y-m-d H:i:s'), $now->format('Y-m-d H:i:s')]);
            $field_names = [];
            foreach ($fields as $field){
                $name = $columns[$field->field];
                $field_names[$name] = $field->value;
                $query->where($field->field, '=', $field->value);
            }
            $count = $query->count();
            if($count < $amount){
                continue;
            }
            $record = $query->select(['type', 'data', 'len'])->orderByDesc('date')->first();
            $message = 'За последние '.$notification->minutes.' минут загружено '.$count.' ошибок';

            $data = $record->data;
            $limit = 500;
            if($record->len > $limit){
                $data = substr($data, 0, $limit);
            }
            $message = [
                'id'=>$notification->id,
                'notification'=>$notification,
                'name'=>$notification->name,
                'fields'=>$field_names,
                'message'=>$message,
                'data'=>$data
            ];
            $all->put($notification->guid, $message);
        }

        foreach ($all as $notification){

            $option = $notification['notification'];
            if(!NotificationMessage::canSendMessage($option)){
                continue;
            }
            $fields = '*Поля отбора:*'."\n\n\r";
            foreach ($notification['fields'] as $name => $value){
                $fields .= $name.' = '.$value."\n\r";
            }
            $text = '*'.$notification['name'].'*'."\n\n\r".$fields."\n\r".$notification['message']."\n\n\r".'*Текст последней ошибки:*'."\n\n\r";

            $message = new NotificationMessage();
            $message->team = $team;
            $message->option = $option->id;
            $message->date = (new \DateTime())->modify('+3 hours')->format('Y-m-d H:i:s');
            $message->fields = $fields;
            $message->message = $text;
            $message->data = $notification['data'];
            $message->save();
        }
    }

    private static function getColumns($type):array
    {
        if($type === 'errors'){
            $columns = Error::columns();
        }else{
            $columns = Log::columns();
        }
        $list = [];
        collect($columns)->each(function ($item) use (&$list){
            $list[$item['column']] = $item['name'];
        });
        return $list;
    }

}
