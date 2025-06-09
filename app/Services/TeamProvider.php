<?php

namespace App\Services;

use App\Interfaces\ITeamProvider;
use App\Interfaces\ITeamService;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

class TeamProvider implements ITeamProvider
{

    public function __construct(ITeamService $teamService)
    {
        $this->teamService = $teamService;
    }

    public function change($guid): Model|null
    {
        $team = $this->teamService->getByGuid($guid);
        if($team == null){
            return null;
        }
        return $this->teamService->current($team->id);
    }

    public function update()
    {

    }

    public function list(): Collection
    {
        return $this->teamService->list();
    }

    public function create($name): array
    {
        return $this->teamService->create($name);
    }

    public function save()
    {

    }

    public function exclude()
    {

    }

    public function invite()
    {

    }

    public function roleChange()
    {

    }

}
