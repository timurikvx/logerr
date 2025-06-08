<?php

namespace App\Services;

use App\Interfaces\IListPreferences;
use App\Interfaces\IListProvider;
use App\Interfaces\IListSettings;
use App\Interfaces\IListSettingsService;
use App\Interfaces\ITeamService;
use App\Interfaces\IUserSettingsService;
use Illuminate\Http\Request;
use App\Interfaces\IListModel;


class ListSettings implements IListSettings
{

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
        if(!is_null($option)){
            $this->userSettingsService->set($provider->prefix().'_current_option', $team, $option->guid);
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

    public function changeSetting()
    {

    }

    public function getSettingData(Request $request): array
    {
        return [
            'filters'=>$request->get('filters', []),
            'sort'=>$request->get('sort', []),
            'columns'=>$request->get('columns', [])
        ];
    }

    private function removePreferences(IListModel $provider, $team): void
    {
        $this->userSettingsService->remove($provider->cacheSort(), $team->id);
        $this->userSettingsService->remove($provider->cacheFilters(), $team->id);
        $this->userSettingsService->remove($provider->cacheColumns(), $team->id);
    }
}
