<?php

namespace App\Interfaces;

use App\Interfaces\Models\TeamInterface;
use App\Interfaces\Models\TelegramChatInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface TelegramChatProviderInterface
{
    function create(TeamInterface $team, string $name, string $token, string $chat_id): string;

    function update(string $guid, string $name, string $token, string $chat_id): bool;

    function remove(TeamInterface $team, string $guid): bool;

    function chats(TeamInterface $team): Collection;

    function chatsByTeams(Collection $teams): Collection;

    function getByGuid(string $guid): TelegramChatInterface|null;

    function copyChatToTeam(TeamInterface $team, TelegramChatInterface $chat): void;

}
