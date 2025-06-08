<?php

namespace App\Interfaces\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface ITeam
{
    function create(User $user, string $name, string $guid = null): void;

    function check(User $user, string $name): bool;

    function checkGuid(User $user, string $guid): bool;

    function list(User $user): Collection;

    function getByGuid(User $user, string $guid): Model|null;

    function getByID(User $user, int $id): Model|null;

    function addToTeam(User $user, $team, string $role = null): void;

    function roles(): array;

    function getMembers(User $user, $team): Collection;

}
