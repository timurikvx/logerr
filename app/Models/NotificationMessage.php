<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NotificationMessage extends Model
{
    use HasFactory;

    public $timestamps = false;
    public $incrementing = false;

    public static function canSendMessage($option):bool
    {
        $message = self::query()->select([DB::raw('MAX(date) as date')])->where('option', '=', $option->id)->first();
        if(is_null($message)){
            return true;
        }
        $now = (new \DateTime())->modify('+3 hours');
        $date = new \DateTime($message->date);
        $minutes = intval(($now->getTimestamp() - $date->getTimestamp()) / 60);
        if($minutes >= $option->every){
            return true;
        }
        return false;
    }

    public static function handle(): void
    {
        $list = self::query()->where('sended', '=', false)->get();
        foreach ($list as $message){
            $option = NotificationsOption::find($message->option);
            $chat = TelegramChat::find($option->chat);
            $text = $message->message.'`'.$message->data.'`';
            $chat->send($text);
            NotificationMessage::where('team', '=', $message->team)
                ->where('option', '=', $message->option)
                ->where('date', '=', $message->date)
                ->update(['sended'=> true]);
        }
    }


}
