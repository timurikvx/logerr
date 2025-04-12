<?php

namespace App\Http\Controllers;

use App\Http\Resources\Telegram\TelegramChatResource;
use App\Models\Crew;
use App\Models\TelegramChat;
use App\Models\UserOption;
use Illuminate\Http\Request;

class TelegramChatController extends Controller
{
    function save(Request $request): array
    {
        $name = $request->get('name');
        $token = $request->get('token');
        $chat_id = $request->get('chat_id');
        $guid = $request->get('guid');

        $team = UserOption::get('current_team');
        if(!empty($guid)){
            $chat = TelegramChat::getByGuid($guid);
            $chat->name = $name;
            $chat->token = $token;
            $chat->chat_id = $chat_id;
            $chat->save();
        }else{
            TelegramChat::create($name, $token, $chat_id, $team);
        }
        $chats = TelegramChat::getChats($team);
        return [
            'list'=>(TelegramChatResource::collection($chats))->toArray($request)
        ];
    }

    function remove(Request $request): array
    {
        $guid = $request->get('guid');
        $team = UserOption::get('current_team');
        TelegramChat::remove($guid, $team);
        $chats = TelegramChat::getChats($team);
        return [
            'list'=>(TelegramChatResource::collection($chats))->toArray($request)
        ];
    }

    function getFromTeams(Request $request): array
    {
        $teams = Crew::list();
        $team = UserOption::get('current_team');
        $list = $teams->filter(function ($item) use ($team){
            return $item->id !== intval($team);
        })->pluck('id')->toArray();

        $chats = TelegramChat::query()->whereIn('team', $list)->orderBy('name')->get();
        return [
            'chats'=>(TelegramChatResource::collection($chats))->toArray($request)
        ];
    }

}
