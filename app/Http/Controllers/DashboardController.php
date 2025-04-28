<?php

namespace App\Http\Controllers;

use App\Actions\Filters;
use App\Actions\PageOptions;
use App\Actions\Report;
use App\Http\Resources\Crew\CrewItemResource;
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

    public function dashboard(Request $request): Response
    {
        $team_id = UserOption::get('current_team', 0);
        $team = Crew::getByID($team_id);

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


        $data = PageOptions::get();
        $data->put('title', 'Панель управления');
        $data->put('reports', $reports);
        $data->put('teams', CrewItemResource::collection(Crew::list())->toArray($request));
        if(is_null($team)){
            $data->put('team', []);
        }else{
            $data->put('team', (new CrewItemResource($team))->toArray($request));
        }
        return Inertia::render('Dashboard', $data);
    }

    public function filters(Request $request): array
    {
        return Filters::equalsByTypes();
    }

    public function choice(Request $request): array
    {
        $value = $request->get('value');
        $type = $request->get('type');
        $field = $request->get('field');

        $list = [];
        if($type === 'error'){
            $list = LogerrNames::query()
                ->select('value')
                ->where('type', '=', 'errors')
                ->where('field', '=', $field)
                ->where('value', 'ILIKE', $value.'%')
                ->limit(20)->orderBy('value')->get();
            //$list = Error::query()->select([$field])->where($field, 'ILIKE', $value.'%')->limit(20)->distinct()->orderBy($field)->get();

        }
        return [
            'list'=>$list->pluck('value')
            //'list'=>$list->pluck($field)
        ];
    }

    public function teamChange(Request $request): array
    {
        $guid = $request->get('team');
        $team = Crew::getByGuid($guid);
        if(is_null($team)){
            return [];
        }
        UserOption::set('current_team', 0, $team->id);

        $data_team = Report::getTodayErrors($team->id);
        $report = ['data'=>$data_team->pluck('value', 'name'), 'guid'=>$team->guid, 'team'=>$team->name];
        $reports = [
            'today'=>$report
        ];
        return [
            'reports'=>$reports
        ];
    }

}
