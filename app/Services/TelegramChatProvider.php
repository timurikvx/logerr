<?php

namespace App\Services;

use App\Interfaces\Models\TeamInterface;
use App\Interfaces\Models\TelegramChatInterface;
use App\Interfaces\TelegramChatProviderInterface;
use App\Models\TelegramChat;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Enumerable;
use Illuminate\Database\Eloquent\Model;
class TelegramChatProvider implements TelegramChatProviderInterface
{

    public function __construct()
    {

    }

    public function create(TeamInterface $team, string $name, string $token, string $chat_id): string
    {
        return TelegramChat::create($name, $token, $chat_id, $team->getID());
    }

    public function update(string $guid, string $name, string $token, string $chat_id): bool
    {
        $chat = $this->getByGuid($guid);
        if($chat == null){
            return false;
        }
        $chat->name = $name;
        $chat->token = $token;
        $chat->chat_id = $chat_id;
        $chat->save();
        return true;
    }

    public function remove(TeamInterface $team, string $guid): bool
    {
        return TelegramChat::remove($guid, $team->getID());
    }

    public function chats(TeamInterface $team): Collection
    {
        return TelegramChat::getChats($team->getID());
    }

    public function chatsByTeams(Enumerable $teams): Collection
    {
        $ids = $teams->pluck('id')->values()->toArray();
        return TelegramChat::query()->whereIn('team', $ids)->get();
    }

    public function getByGuid(string $guid): TelegramChatInterface|null
    {
        /** @var TelegramChatInterface $chat */
        $chat = TelegramChat::getByGuid($guid);
        return $chat;
        //return TelegramChat::getByGuid($guid);
    }

    public function copyChatToTeam(TeamInterface $team, TelegramChatInterface $chat): void
    {
        $this->create($team, $chat->getName(), $chat->getToken(), $chat->getChatID());
    }

}
