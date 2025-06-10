<?php

namespace App\Http\Controllers\Errors;

use App\Http\Controllers\Controller;
use App\Interfaces\IListSettings;
use App\Interfaces\IListProvider;
use App\Interfaces\ITeamProvider;
use App\Models\Error;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;

class ErrorController extends Controller
{

    public function __construct(
        IListProvider        $listProvider,
        IListSettings        $listSettings,
        ITeamProvider        $teamProvider
    )
    {
        parent::__construct();
        $this->listProvider  = $listProvider;
        $this->listSettings  = $listSettings;
        $this->teamProvider    = $teamProvider;
    }

    public function errors(Request $request): mixed
    {
        $guid = $request->get('set-team');
        $team = $this->teamProvider->current($guid);
        if(is_null($team)){
            return redirect()->route('teams');
        }
        $title = 'Список ошибок';
        $provider = new Error();
        $data = $this->listProvider->list($provider, $team, $request);
        $data->put('title', $title);
        $data->put('head', $title);
        return Inertia::render('MainList', $data);
    }

    public function changeSetting(Request $request): array
    {
        $setting = $request->get('guid');
        $team = $this->teamProvider->current();
        $provider = new Error();
        $this->listSettings->changeSettings($provider, $team, $setting);
        return $this->listProvider->updateList($provider, $team);
    }

    public function createSetting(Request $request): array
    {
        $name = $request->get('name');
        $team = $this->teamProvider->current();
        $provider = new Error();
        $data = $this->listSettings->getSettingData($request);
        return $this->listSettings->createSetting($provider, $name, $team, $data);
    }

    public function removeSetting(Request $request): array
    {
        $team = $this->teamProvider->current();
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

        $this->listSettings->saveFilters($provider, $filters);
        $this->listSettings->saveSort($provider, $sort);
        $this->listSettings->saveColumns($provider, $columns);

        return ['result'=>true];
    }

    public function clearPreferences(Request $request): array
    {
        $provider = new Error();
        $field = $request->get('field');
        $this->listSettings->clearCondition($provider, $field);
        return ['result'=>true];
    }

    public function filter(Request $request): array
    {
        $team = $this->teamProvider->current();
        $filters = $request->get('filter');
        $sort = $request->get('sort');

        $provider = new Error();
        $data = $this->listProvider->saveFilters($provider, $team, $filters, $sort);
        return [
            'list'=>$data->data,
            'paginate'=>$data->paginate
        ];
    }

    public function changeTeam(Request $request): Collection
    {
        $guid = $request->get('team');
        $team = $this->teamProvider->current($guid);
        $provider = new Error();
        return $this->listProvider->list($provider, $team, $request);
    }

    public function page(Request $request): array
    {
        $team = $this->teamProvider->current();
        $provider = new Error();
        $filters = $this->listSettings->getFilters($provider);
        $sort = $this->listSettings->getSort($provider);
        $data = $this->listProvider->get($provider, $team, $filters, $sort);
        return [
            'list'=>$data->data,
            'paginate'=>$data->paginate
        ];
    }

}
