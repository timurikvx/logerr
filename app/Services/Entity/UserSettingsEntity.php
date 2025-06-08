<?php

namespace App\Services\Entity;

use App\Models\Crew;
use App\Models\User;
use App\Interfaces\IUserSettingsEntity;

class UserSettingsEntity implements IUserSettingsEntity
{

    function __construct()
    {

    }

    function set(User $user, string $name, Crew $team, mixed $value): bool
    {
        return true;
    }

    function get(User $user, string $name, Crew $team, $default = null): mixed
    {
        return '';
    }

    function remove(User $user, string $name, Crew $team): void
    {

    }

}
