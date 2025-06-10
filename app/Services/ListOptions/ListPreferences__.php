<?php

namespace App\Services\ListOptions;

use App\Interfaces\IListModel;
use App\Interfaces\IListPreferences;
use App\Interfaces\ILogerrCache;
use App\Interfaces\ITeamService;
use App\Interfaces\IUserSettingsService;

//Not use
class ListPreferences implements IListPreferences
{
    //Not use
    private ILogerrCache $cache;
    private ITeamService $teamService;
    private IUserSettingsService $userSettingsService;

    public function __construct(
        IUserSettingsService $userSettingsService,
        ITeamService         $teamService,
        ILogerrCache         $cache
    )
    {
        $this->userSettingsService = $userSettingsService;
        $this->teamService = $teamService;
        $this->cache = $cache;
    }

    public function saveFilters(IListModel $provider, $data): void
    {
        if(is_null($data)){
            return;
        }
        $team = $this->teamService->current();
        if(is_null($team)){
            return;
        }
        $this->userSettingsService->set($provider->cacheFilters(), $team->id, $data);
    }

    public function saveSort(IListModel $provider, $data): void
    {
        if(is_null($data)){
            return;
        }
        $team = $this->teamService->current();
        if(is_null($team)){
            return;
        }
        $this->userSettingsService->set($provider->cacheSort(), $team->id, $data);
    }

    public function saveColumns(IListModel $provider, $data): void
    {
        if(is_null($data)){
            return;
        }
        $team = $this->teamService->current();
        if(is_null($team)){
            return;
        }
        $this->userSettingsService->set($provider->cacheColumns(), $team->id, $data);
    }

    public function clear(IListModel $provider, $field): void
    {
        $team = $this->teamService->current();
        $this->userSettingsService->remove($provider->prefix().'_'.$field, $team->id);
    }

}
