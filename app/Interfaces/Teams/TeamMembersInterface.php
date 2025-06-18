<?php

namespace App\Interfaces\Teams;

use App\Interfaces\Models\TeamInterface;
use App\Interfaces\Models\UserInterface;
use Illuminate\Support\Enumerable;

interface TeamMembersInterface
{
    function getTextInvite(TeamInterface $team): string;

    function getTitleInvite(TeamInterface $team): string;

    function getTextExclude(TeamInterface $team): string;

    function getTitleExclude(TeamInterface $team): string;

    function join(int $user, int $team): bool;

    function exclude(UserInterface $user, TeamInterface $team): bool;

    function changeRole(TeamInterface $team, UserInterface $user, string $role): bool;

    function inviteEvent(): string;

    function changeRoleEvent(): string;

    function excludeEvent(): string;

    function roles(): array;

}
