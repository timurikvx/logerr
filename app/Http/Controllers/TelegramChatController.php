<?php

namespace App\Http\Controllers;

use App\Http\Resources\Telegram\TelegramChatResource;
use App\Models\TelegramChat;
use App\Models\UserOption;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class TelegramChatController extends Controller
{
    function save(Request $request): array
    {
        $name = $request->get('name');
        $token = $request->get('token');
        $chat_id = $request->get('chat_id');

        $team = UserOption::get('current_team');
        TelegramChat::create($name, $token, $chat_id, $team);
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
}
