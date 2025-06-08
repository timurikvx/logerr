<?php

namespace App\Interfaces;

use App\Models\Crew;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface ITeamService
{
//    public function createTeam($name, $guid = null): int;
//
//    public function inTeam(string $name): bool;
//
//    public function inTeamByGuid(string $guid):bool;
//
//    public function userTeams(): Collection;
//
//    public function getByGuid(string $guid): Model|null;
//
//    public function getByID(int $id): Model|null;
//
//    public function append(int $user, int $team, $role = null): void;
//
//    public function roles(): array;
//
//    public function getMembers(int $team): Collection;

    public function current(int $team = null): Crew|null;

}
