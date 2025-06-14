<?php

namespace App\Interfaces\Models;

interface TelegramChatInterface
{
    function getID(): int;

    function getGuid(): string;

    function getName(): string;

    function getToken(): string;

    function getChatID(): string;

}
