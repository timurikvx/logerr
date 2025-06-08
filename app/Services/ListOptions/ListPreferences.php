<?php

namespace App\Services\ListOptions;

use App\Interfaces\IListModel;
use App\Interfaces\IListPreferences;
use App\Interfaces\IListSettingsService;
use App\Interfaces\ILogerrCache;
use App\Interfaces\ITeamService;
use App\Interfaces\IUserSettingsService;

class ListPreferences implements IListPreferences
{
    public function __construct(
        IUserSettingsService $userSettingsService,
        ITeamService         $teamService,
        IListSettingsService $listSettingsService,
        ILogerrCache         $cache
    )
    {
        $this->userSettingsService = $userSettingsService;
        $this->teamService = $teamService;
        $this->listSettingsService = $listSettingsService;
        $this->cache = $cache;
    }

    public function columns($provider): array
    {
        $team = $this->teamService->current();
        $setting = $this->listSettingsService->current($team, $provider->prefix());
        if(!is_null($setting)){
            $columns = $setting['data']['columns'];
        }else{
            $columns = $this->userSettingsService->get($provider->cacheColumns(), $team->id);
        }
        if(is_null($columns)){
            return $provider->availableColumns();
        }
        return $columns;
    }

    public function sort($provider): array
    {
        $team = $this->teamService->current();
        $listOption = $this->listSettingsService->current($team, $provider->prefix());
        if(!is_null($listOption)){
            return $listOption['data']['sort'];
        }else{
            return $this->userSettingsService->get($provider->cacheSort(), $team->id, []);
        }
    }

    public function filters($provider): array
    {
        $team = $this->teamService->current();
        $setting = $this->listSettingsService->current($team, $provider->prefix());
        if(!is_null($setting)){
            $filters = $setting['data']['filters'];
        }else{
            $filters = $this->userSettingsService->get($provider->cacheFilters(), $team->id, []);
        }
        if(empty($filters)){
            $filters = $provider->getFilters();
        }
        return $filters;
    }

    public function saveFilters(IListModel $provider, $data): void
    {
        if(is_null($data)){
            return;
        }
        $team = $this->teamService->current();
        $this->userSettingsService->set($provider->cacheFilters(), $team, $data);
    }

    public function saveSort(IListModel $provider, $data): void
    {
        if(is_null($data)){
            return;
        }
        $team = $this->teamService->current();
        $this->userSettingsService->set($provider->cacheSort(), $team, $data);
    }

    public function saveColumns(IListModel $provider, $data): void
    {
        if(is_null($data)){
            return;
        }
        $team = $this->teamService->current();
        $this->userSettingsService->set($provider->cacheColumns(), $team, $data);
    }

    public function remove($provider): void
    {
        $team = $this->teamService->current();
        $this->userSettingsService->remove($provider->prefix().'_current_option', $team->id);
    }

    public function clear(IListModel $provider, $field): void
    {
        $team = $this->teamService->current();
        $this->userSettingsService->remove($provider->prefix().'_'.$field, $team->id);
    }

}
