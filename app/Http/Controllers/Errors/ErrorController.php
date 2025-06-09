<?php

namespace App\Http\Controllers\Errors;

use App\Http\Controllers\Controller;
use App\Interfaces\IListSettings;
use App\Interfaces\IListProvider;
use App\Interfaces\ITeamService;
use App\Models\Error;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Interfaces\IListPreferences;

class ErrorController extends Controller
{

    public function __construct(
        ITeamService         $teamService,
        IListProvider        $listProvider,
        IListSettings        $listSettings,
        IListPreferences     $listPreferences
    )
    {
        parent::__construct();
        $this->teamService = $teamService;
        $this->listProvider  = $listProvider;
        $this->listSettings  = $listSettings;
        $this->listPreferences = $listPreferences;
    }

    public function errors(Request $request): mixed
    {
        $setTeam = $request->get('set-team');
        $team = $this->teamService->current($setTeam);
        if(is_null($team)){
            return redirect()->route('teams');
        }

        $title = 'Список ошибок';
        $provider = new Error();
        $data = $this->listProvider->list($provider, $team, $title, $request);
        return Inertia::render('MainList', $data);
    }

    public function changeSetting(Request $request): array
    {
        $guid = $request->get('guid');
        $team = $this->teamService->current();
        $provider = new Error();
        $this->listSettings->changeSettings($provider, $team, $guid);
        return $this->listProvider->updateList($provider, $team);
    }

    public function createSetting(Request $request): array
    {
        $name = $request->get('name');
        $team = $this->teamService->current();
        $provider = new Error();
        $data = $this->listSettings->getSettingData($request);
        return $this->listSettings->createSetting($provider, $name, $team, $data);
    }

    public function removeSetting(Request $request): array
    {
        $team = $this->teamService->current();
        $guid = $request->get('guid');
        $provider = new Error();
        $this->listSettings->removeSetting($provider, $guid);
        return $this->listProvider->updateList($provider, $team);
    }

    public function saveSetting(Request $request): array
    {
        $provider = new Error();
        $guid = $request->get('guid');
        $data = $this->listSettings->getSettingData($request);
        return $this->listSettings->saveSetting($provider, $guid, $data);
    }

    public function setPreferences(Request $request): array
    {
        $provider = new Error();
        $filters = $request->get('filters');
        $sort = $request->get('sort');
        $columns = $request->get('columns');

        $this->listPreferences->saveFilters($provider, $filters);
        $this->listPreferences->saveSort($provider, $sort);
        $this->listPreferences->saveColumns($provider, $columns);

        return ['result'=>true];
    }

    public function clearPreferences(Request $request): array
    {
        $provider = new Error();
        $field = $request->get('field');
        $this->listPreferences->clear($provider, $field);
        return ['result'=>true];
    }

    public function filter(Request $request): array
    {
        $team = $this->teamService->current();
        $filters = $request->get('filter');
        $sort = $request->get('sort');

        $provider = new Error();
        $this->listPreferences->saveFilters($provider, $filters);
        $this->listPreferences->saveSort($provider, $sort);

        $data = $this->listProvider->get($provider, $team, $filters, $sort);
        return [
            'list'=>$data->data,
            'paginate'=>$data->paginate
        ];
    }

}
