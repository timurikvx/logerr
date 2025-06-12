<?php

namespace App\Http\Controllers\Logs;

use App\Http\Controllers\Controller;
use App\Interfaces\IListProvider;
use App\Interfaces\IListSettings;
use App\Interfaces\ITeamProvider;
use App\Interfaces\ITeamService;
use App\Models\Log;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LogsController extends Controller
{
    private ITeamProvider $teamProvider;
    private IListProvider $listProvider;
    private IListSettings $listSettings;

    public function __construct(
        ITeamProvider        $teamProvider,
        IListProvider        $listProvider,
        IListSettings        $listSettings
    )
    {
        parent::__construct();
        $this->teamProvider = $teamProvider;
        $this->listProvider  = $listProvider;
        $this->listSettings  = $listSettings;
    }

    public function logs(Request $request): mixed
    {
        $setTeam = $request->get('set-team');
        $team = $this->teamProvider->current($setTeam);
        if(is_null($team)){
            return redirect()->route('teams');
        }

        $title = 'Список ошибок';
        $provider = new Log();
        $data = $this->listProvider->list($provider, $team, $request);
        $data->put('title', $title);
        $data->put('head', $title);
        return Inertia::render('MainList', $data);
    }

    public function changeSetting(Request $request): array
    {
        $setting = $request->get('guid');
        $team = $this->teamProvider->current();
        $provider = new Log();
        $this->listSettings->changeSettings($provider, $team, $setting);
        return $this->listProvider->updateList($provider, $team);
    }

    public function createSetting(Request $request): array
    {
        $name = $request->get('name');
        $team = $this->teamProvider->current();
        $provider = new Log();
        $data = $this->listSettings->getSettingData($request);
        return $this->listSettings->createSetting($provider, $name, $team, $data);
    }

}
