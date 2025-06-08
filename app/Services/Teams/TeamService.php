<?php

namespace App\Services\Teams;

use App\Interfaces\ILogerrCache;
use App\Interfaces\ITeamService;
use App\Interfaces\IUserSettingsService;
use App\Models\Crew;

class TeamService implements ITeamService
{

    public function __construct(ILogerrCache $cache, IUserSettingsService $userOptionService)
    {
        $this->cache = $cache;
        $this->userOption = $userOptionService;
    }

//    public function createTeam($name, $guid = null): int
//    {
//        return Crew::create($name, $guid);
//    }
//
//    public function inTeam(string $name): bool
//    {
//        return Crew::check($name);
//    }
//
//    public function inTeamByGuid(string $guid):bool
//    {
//        return Crew::checkGuid($guid);
//    }
//
//    public function userTeams(): Collection
//    {
//        return Crew::list();
//    }
//
//    public function getByGuid(string $guid): Model|null
//    {
//        return Crew::getByGuid($guid);
//    }
//
//    public function getByID(int $id): Model|null
//    {
//        return Crew::getByGuid($id);
//    }
//
//    public function append(int $user, int $team, $role = null): void
//    {
//        Crew::addToTeam($user, $team, $role);
//    }
//
//    public function roles(): array
//    {
//        return Crew::roles();
//    }
//
//    public function getMembers(int $team): Collection
//    {
//        return Crew::getMembers($team);
//    }

    public function current(int $team = null): Crew|null
    {
        if(!is_null($team)){
            $teamItem = Crew::find($team);
            $this->userOption->set('current_team', 0, $teamItem);
        }
        return $this->cache->get('current_team', function (){
            $team_id = $this->userOption->get('current_team', 0);
            return Crew::find($team_id);
        }, 30);
    }

}
