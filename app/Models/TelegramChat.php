<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Ramsey\Uuid\Uuid;

class TelegramChat extends Model
{
    use HasFactory;

    public static function create($name, $token, $chat_id, $team): string|null
    {
        if(empty($team)){
            return null;
        }
        $chat = new TelegramChat();
        $chat->team = $team;
        $chat->name = $name;
        $chat->token = $token;
        $chat->chat_id = $chat_id;
        $chat->guid = Uuid::uuid4()->toString();
        $chat->creator = Auth::id();
        $chat->save();
        return $chat->guid;
    }

    public static function remove($guid, $team): bool
    {
        return self::query()
            ->where('team', '=', $team)
            ->where('guid', '=', $guid)
            ->delete();
    }

    public static function getChats($team): Collection
    {
        return self::query()->where('team', '=', $team)->get();
    }

    public static function getByGuid($guid): Model|null
    {
        return self::query()->where('guid', '=', $guid)->first();
    }

    public function send($text):void
    {
        $data = [
            'text'=>$text,
            'chat_id'=>$this->chat_id
        ];
        Http::post('https://api.telegram.org/bot'.$this->token.'/sendMessage', $data);
    }

}
