<?php

namespace App\Http\Controllers;

use App\Actions\Filters;
use App\Actions\PageOptions;
use App\Actions\Report;
use App\Http\Resources\Crew\CrewItemResource;
use App\Interfaces\Models\TeamInterface;
use App\Interfaces\TeamProviderInterface;
use App\Models\Crew;
use App\Models\Error;
use App\Models\LogerrNames;
use App\Models\User;
use App\Models\UserOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{

    private TeamProviderInterface $teamProvider;

    public function __construct(TeamProviderInterface $teamProvider)
    {
        parent::__construct();
        $this->teamProvider = $teamProvider;
    }

    public function dashboard(Request $request): Response
    {
        $team = $this->teamProvider->current();

        $report = [];
        $to5days = [];
        if(!is_null($team)){
            $data_team = Report::getTop5TodayErrors($team->id);
            $report = $data_team->pluck('value', 'name');
            $to5days = Report::get5daysErrors($team->id);
        }

        $reports = [
            'today'=>$report,
            'five_days'=>$to5days
        ];

        $teams = $this->teamProvider->list();

        $data = PageOptions::get();
        $data->put('title', 'Панель управления');
        $data->put('reports', $reports);
        $data->put('teams', CrewItemResource::collection($teams)->toArray($request));
        if(is_null($team)){
            $data->put('team', []);
        }else{
            $data->put('team', (new CrewItemResource($team))->toArray($request));
        }
        return Inertia::render('Dashboard', $data);
    }

}
