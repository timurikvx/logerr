<?php

namespace App\Http\Controllers\Logs;

use App\Http\Controllers\Controller;
use App\Interfaces\ListModelInterface;
use App\Interfaces\ListProviderInterface;
use App\Interfaces\ListSettingsInterface;
use App\Interfaces\TeamProviderInterface;
use App\Interfaces\TeamServiceInterface;
use App\Models\Error;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;

class LogsController extends Controller
{
    private TeamProviderInterface $teamProvider;
    private ListProviderInterface $listProvider;
    private ListSettingsInterface $listSettings;
    private ListModelInterface $provider;

    public function __construct(
        TeamProviderInterface $teamProvider,
        ListProviderInterface $listProvider,
        ListSettingsInterface $listSettings
    )
    {
        parent::__construct();
        $this->teamProvider = $teamProvider;
        $this->listProvider  = $listProvider;
        $this->listSettings  = $listSettings;
        $this->provider = new Log();
    }

    public function logs(Request $request): mixed
    {
        $setTeam = $request->get('set-team');
        $team = $this->teamProvider->current($setTeam);
        if(is_null($team)){
            return redirect()->route('teams');
        }

        $title = 'Список ошибок';
        $data = $this->listProvider->list($this->provider, $team, $request);
        $data->put('title', $title);
        $data->put('head', $title);
        return Inertia::render('MainList', $data);
    }

    public function changeSetting(Request $request): array
    {
        $setting = $request->get('guid');
        $team = $this->teamProvider->current();
        $this->listSettings->changeSettings($this->provider, $team, $setting);
        return $this->listProvider->updateList($this->provider, $team);
    }

    public function createSetting(Request $request): array
    {
        $name = $request->get('name');
        $team = $this->teamProvider->current();
        $data = $this->listSettings->getSettingData($request);
        return $this->listSettings->createSetting($this->provider, $name, $team, $data);
    }

    public function removeSetting(Request $request): array
    {
        $team = $this->teamProvider->current();
        $guid = $request->get('guid');
        $this->listSettings->removeSetting($this->provider, $guid);
        return $this->listProvider->updateList($this->provider, $team);
    }

    public function saveSetting(Request $request): array
    {
        $guid = $request->get('guid');
        $data = $this->listSettings->getSettingData($request);
        return $this->listSettings->saveSetting($this->provider, $guid, $data);
    }

    public function setPreferences(Request $request): array
    {
        $filters = $request->get('filters');
        $sort = $request->get('sort');
        $columns = $request->get('columns');

        $this->listSettings->saveFilters($this->provider, $filters);
        $this->listSettings->saveSort($this->provider, $sort);
        $this->listSettings->saveColumns($this->provider, $columns);

        return ['result'=>true];
    }

    public function clearPreferences(Request $request): array
    {
        $field = $request->get('field');
        $this->listSettings->clearCondition($this->provider, $field);
        return ['result'=>true];
    }

    public function filter(Request $request): array
    {
        $team = $this->teamProvider->current();
        $filters = $request->get('filter');
        $sort = $request->get('sort');

        $data = $this->listProvider->saveFilters($this->provider, $team, $filters, $sort);
        return [
            'list'=>$data->data,
            'paginate'=>$data->paginate
        ];
    }

    public function changeTeam(Request $request): Collection
    {
        $guid = $request->get('team');
        $team = $this->teamProvider->current($guid);
        return $this->listProvider->list($this->provider, $team, $request);
    }

    public function page(Request $request): array
    {
        $team = $this->teamProvider->current();
        $filters = $this->listSettings->getFilters($this->provider);
        $sort = $this->listSettings->getSort($this->provider);
        $data = $this->listProvider->get($this->provider, $team, $filters, $sort);
        return [
            'list'=>$data->data,
            'paginate'=>$data->paginate
        ];
    }


}
