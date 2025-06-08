<?php

namespace App\Interfaces;

use App\Models\Crew;
use App\Models\User;

interface IUserSettingsEntity
{
    function set(User $user, string $name, Crew $team, mixed $value): bool;

    function get(User $user, string $name, Crew $team, $default = null): mixed;

    function remove(User $user, string $name, Crew $team): void;

}
