<?php

namespace App\Http\Controllers;

use App\Http\Resources\Telegram\TelegramChatResource;
use App\Interfaces\ITeamProvider;
use App\Interfaces\ITeamService;
use App\Interfaces\TelegramChatProviderInterface;
use App\Models\Crew;
use App\Models\TelegramChat;
use App\Models\UserOption;
use Illuminate\Http\Request;

class TelegramChatController extends Controller
{

    private ITeamProvider $teamProvider;
    private TelegramChatProviderInterface $telegramChatProvider;

    public function __construct(ITeamProvider $teamProvider, TelegramChatProviderInterface $telegramChatProvider)
    {
        parent::__construct();
        $this->teamProvider = $teamProvider;
        $this->telegramChatProvider = $telegramChatProvider;
    }

    function save(Request $request): array
    {
        $name = $request->get('name');
        $token = $request->get('token');
        $chat_id = $request->get('chat_id');
        $guid = $request->get('guid');

        $team = $this->teamProvider->current();
        if(empty($guid)){
            $this->telegramChatProvider->create($team, $name, $token, $chat_id);
        }else{
            $this->telegramChatProvider->update($guid, $name, $token, $chat_id);
        }
        $chats = $this->telegramChatProvider->chats($team);
        return [
            'list'=>(TelegramChatResource::collection($chats))->toArray($request)
        ];
    }

    function remove(Request $request): array
    {
        $guid = $request->get('guid');
        $team = $this->teamProvider->current();
        $this->telegramChatProvider->remove($team, $guid);
        $chats = $this->telegramChatProvider->chats($team);
        return [
            'list'=>(TelegramChatResource::collection($chats))->toArray($request)
        ];
    }

    function getFromTeams(Request $request): array
    {
        $team = $this->teamProvider->current();
        $teams = $this->teamProvider->listWithout(collect([$team]));
        $chats = $this->telegramChatProvider->chatsByTeams($teams);
        return [
            'chats'=>(TelegramChatResource::collection($chats))->toArray($request)
        ];
    }

    public function copyTeams(Request $request): array
    {
        $team = $this->teamProvider->current();
        $list = $request->get('chats');
        foreach ($list as $guid){
            $chat = $this->telegramChatProvider->getByGuid($guid);
            if(is_null($chat)){
                continue;
            }
            $this->telegramChatProvider->copyChatToTeam($team, $chat);
        }
        $chats = $this->telegramChatProvider->chats($team);
        return [
            'list'=>(TelegramChatResource::collection($chats))->toArray($request)
        ];
    }

}
