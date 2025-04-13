<?php

namespace App\Http\Controllers;

use App\Actions\Filters;
use App\Actions\PageOptions;
use App\Actions\Report;
use App\Http\Resources\Crew\CrewItemResource;
use App\Models\Crew;
use App\Models\Error;
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
        if(!is_null($team)){
            $data_team = Report::getTodayErrors($team->id);
            $report = $data_team->pluck('value', 'name');
        }

        $reports = [
            'today'=>$report
        ];
        //dump($reports);

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
            $list = Error::query()->select([$field])->where($field, 'ILIKE', $value.'%')->limit(20)->distinct()->orderBy($field)->get();

        }
        return [
            'list'=>$list->pluck($field)
        ];
    }

//    public function test(Request $request): array
//    {
//        $data = collect([]);
//        Auth::login(User::find(1));
//        $teams = Crew::list();
//        foreach ($teams as $team){
//            $data_team = Report::getTodayErrors($team->id);
//            $data[$team->name]= ['series'=>$data_team->pluck('value'), 'columns'=>$data_team->pluck('name')];
//        }
//
////        $data = Report::getTodayErrors(1);
////        $data = $data->sortBy('name');
////        $columns = $data->pluck('name');
////        dump($columns);
////        dump($data->pluck('value'));
//        return ['result'=>true];
//    }

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
