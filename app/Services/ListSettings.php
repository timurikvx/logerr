<?php

namespace App\Services;

use App\Interfaces\IListSettings;
use App\Interfaces\IListSettingsService;
use App\Interfaces\ITeamService;
use App\Interfaces\IUserSettingsService;
use Illuminate\Http\Request;
use App\Interfaces\IListModel;

class ListSettings implements IListSettings
{

    private IUserSettingsService $userSettingsService;
    private IListSettingsService $listSettingsService;
    private ITeamService $teamService;

    public function __construct(
        ITeamService $teamService,
        IListSettingsService $listSettingsService,
        IUserSettingsService $userSettingsService
    )
    {
        $this->teamService = $teamService;
        $this->listSettingsService = $listSettingsService;
        $this->userSettingsService = $userSettingsService;
    }

    public function current(IListModel $provider, $option = null)
    {
        $team = $this->teamService->current();
        if(is_null($team)){
            return null;
        }
        if(!is_null($option)){
            $this->userSettingsService->set($provider->prefix().'_current_option', $team->id, $option->guid);
        }
        $guid = $this->userSettingsService->get($provider->prefix().'_current_option', $team->id);
        return $this->listSettingsService->getByGuid($team, $guid, $provider->prefix());
    }

    public function settings(IListModel $provider): array
    {
        $team = $this->teamService->current();
        return $this->listSettingsService->getAll($team->id, $provider->prefix());
    }

    public function changeSettings(IListModel $provider, $team, $guid): void
    {
        $setting = $this->listSettingsService->get($team->id, $guid, $provider->prefix());
        //change setting
        if(is_null($setting)){
            $this->userSettingsService->remove($provider->prefix().'_current_option', $team->id);
        }else{
            $this->current($provider, $setting);
        }
    }

    public function createSetting(IListModel $provider, string $name, $team, array $data): array
    {
        $guid = $this->listSettingsService->set($team->id, $name, $data, $provider->prefix());

        $this->removePreferences($provider, $team);

        $settings = $this->settings($provider);
        $setting = $this->listSettingsService->getByGuid($team, $guid, $provider->prefix());
        return ['result'=>true, 'options'=>$settings, 'option'=>$setting];
    }

    public function removeSetting(IListModel $provider, string $guid): void
    {
        $team = $this->teamService->current();
        $this->listSettingsService->removeByGuid($team->id, $guid);
    }

    public function saveSetting(IListModel $provider, $guid, $data): array
    {
        $team = $this->teamService->current();
        $this->listSettingsService->updateByGuid($team->id, $guid, $data, $provider->prefix());

        $this->removePreferences($provider, $team);

        $settings = $this->settings($provider);
        $setting = $this->listSettingsService->getByGuid($team, $guid, $provider->prefix());
        return ['result'=>true, 'options'=>$settings, 'option'=>$setting];
    }

    public function getSettingData(Request $request): array
    {
        return [
            'filters'=>$request->get('filters', []),
            'sort'=>$request->get('sort', []),
            'columns'=>$request->get('columns', [])
        ];
    }

    public function removePreferences(IListModel $provider, $team): void
    {
        $this->userSettingsService->remove($provider->cacheSort(), $team->id);
        $this->userSettingsService->remove($provider->cacheFilters(), $team->id);
        $this->userSettingsService->remove($provider->cacheColumns(), $team->id);
    }

    public function getFilters($provider): array
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

    public function getSort($provider): array
    {
        $team = $this->teamService->current();
        $listOption = $this->listSettingsService->current($team, $provider->prefix());
        if(!is_null($listOption)){
            return $listOption['data']['sort'];
        }else{
            return $this->userSettingsService->get($provider->cacheSort(), $team->id, []);
        }
    }

    public function getColumns($provider): array
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

    public function clearCondition(IListModel $provider, $field): void
    {
        $team = $this->teamService->current();
        $this->userSettingsService->remove($provider->prefix().'_'.$field, $team->id);
    }

}
