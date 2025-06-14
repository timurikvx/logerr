<?php

namespace App\Interfaces\Models;

interface UserNotificationInterface
{

    function getID(): int;

    function getName(): string;

    function getGuid(): string;

    function getType(): string;

    function getChat();

    function getMinutes(): int;

    function getCount(): int;

    function getEvery(): int;

    function isDisable(): bool;

}
