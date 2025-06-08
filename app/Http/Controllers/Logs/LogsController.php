<?php

namespace App\Http\Controllers\Logs;

use App\Http\Controllers\Controller;
use App\Interfaces\IListPreferences;
use App\Interfaces\IListProvider;
use App\Interfaces\IListSettings;
use App\Interfaces\ITeamService;
use App\Interfaces\IUserSettingsService;
use App\Models\Error;
use App\Models\Log;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LogsController extends Controller
{
    public function __construct(
        ITeamService         $teamService,
        IListProvider        $listProvider,
        IListSettings        $listSettings
    )
    {
        parent::__construct();
        $this->teamService = $teamService;
        $this->listProvider  = $listProvider;
        $this->listSettings  = $listSettings;
    }

    public function logs(Request $request): mixed
    {
        $setTeam = $request->get('set-team');
        $team = $this->teamService->current($setTeam);
        if(is_null($team)){
            return redirect()->route('teams');
        }

        $start = microtime(true) * 1000;
        $title = 'Список ошибок';

        $provider = new Log();
        $data = $this->listProvider->list($provider, $team, $title, $request);

        return Inertia::render('MainList', $data);
    }

    public function changeSetting(Request $request): array
    {
        $guid = $request->get('guid');
        $team = $this->teamService->current();
        $provider = new Log();
        return $this->listSettings->changeSettings($provider, $team, $guid);
    }

    public function createSetting(Request $request): array
    {
        $name = $request->get('name');
        $team = $this->teamService->current();
        $provider = new Log();
        $data = $this->listSettings->getSettingData($request);
        return $this->listSettings->createSetting($provider, $name, $team, $data);
    }

}
