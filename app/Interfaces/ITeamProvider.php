<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface ITeamProvider
{
    function create($name): array;

    function list(): Collection;

    function change($guid): Model|null;

    function current($guid = null): Model|null;

    function members($team): Collection;

    function get(string $guid): Model|null;

    function rename(&$team, $name): void;

    function invite($inviter, string $email, string $team_guid): array;

}
