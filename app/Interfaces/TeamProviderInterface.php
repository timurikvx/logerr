<?php

namespace App\Interfaces;

use App\Interfaces\Models\TeamInterface;
use App\Interfaces\Models\UserInterface;
use Illuminate\Support\Enumerable;

interface TeamProviderInterface
{
    function create($name): array;

    function list(): Enumerable;

    function change($guid): TeamInterface|null;

    function current($guid = null): TeamInterface|null;

    function members($team): Enumerable;

    function get(string $guid): TeamInterface|null;

    function rename(&$team, $name): void;

    function invite($inviter, string $email, string $team_guid): array;

    function changeRole(TeamInterface $team, UserInterface $user, $role): bool;

    function exclude(UserInterface $user, TeamInterface $team): Enumerable;

    function listWithout(Enumerable $teams): Enumerable;

}
