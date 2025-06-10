<?php

namespace App\Services;

use App\Interfaces\ITeamProvider;
use App\Interfaces\ITeamService;
use App\Models\Crew;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

class TeamProvider implements ITeamProvider
{

    public function __construct(ITeamService $teamService)
    {
        $this->teamService = $teamService;
    }

    public function current($guid = null): Model|null
    {
        if(!is_null($guid)){
            return $this->change($guid);
        }
        return $this->teamService->current();
    }

    public function change($guid): Model|null
    {
        $team = $this->teamService->getByGuid($guid);
        if($team == null){
            return null;
        }
        return $this->teamService->current($team->id);
    }

    public function members($team): Collection
    {
        return Crew::getMembers($team->id);
    }

    public function list(): Collection
    {
        return $this->teamService->list();
    }

    public function create($name): array
    {
        return $this->teamService->create($name);
    }

    public function rename(&$team, $name): void
    {
        Crew::rename($team, $name);
    }

    public function get(string $guid): Model|null
    {
        return Crew::getByGuid($guid);
    }
}
