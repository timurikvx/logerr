<?php

namespace App\Services\Teams;

use App\Interfaces\ILogerrCache;
use App\Interfaces\ITeamService;
use App\Interfaces\IUserSettingsService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\Models\Crew;

class TeamService implements ITeamService
{

    public function __construct(ILogerrCache $cache, IUserSettingsService $userSettingsService)
    {
        $this->cache = $cache;
        $this->userSettingsService = $userSettingsService;
    }

    public function create($name, $guid = null): array
    {
        if(!Crew::check($name)){
            return ['error'=>'Команда с таким именем уже существует'];
        }

        if(!Crew::checkGuid($guid)){
            return ['error'=>'Команда с таким идентификатором уже существует'];
        }
        $guid = Crew::create($name, $guid);
        return ['guid'=>$guid];
    }

    public function list(): Collection
    {
        return Crew::list();
    }

    public function getByGuid(string $guid): Model|null
    {
        return Crew::getByGuid($guid);
    }

    public function current(int $team = null): Model|null
    {
        if(!is_null($team)){
            $teamItem = Crew::find($team);
            $this->userSettingsService->set('current_team', 0, $teamItem->id);
            $this->cache->delete('current_team');
        }
        return $this->cache->get('current_team', function (){
            $team_id = $this->userSettingsService->get('current_team', 0);
            return Crew::getByID($team_id);
        }, 30);
    }

}
