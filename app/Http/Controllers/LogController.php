<?php

namespace App\Http\Controllers;

use App\Http\Resources\Crew\CrewItemResource;
use App\Http\Resources\Errors\ErrorItemResource;
use App\Models\Crew;
use App\Models\LogOption;
use App\Models\Log;
use App\Models\UserOption;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LogController extends Controller
{
    public function logs(Request $request): Response
    {
        $data = ['title'=>'Выбор команды логов'];
        return Inertia::render('Logs/Logs', $data);
    }

    public function logsTeam(Request $request, $guid): Response
    {
        $team = Crew::getByGuid($guid);
        $sort = UserOption::get('logs_sort', $team->id, []);
        $filters = UserOption::get('logs_filters', $team->id, []);
        $columns = UserOption::get('logs_columns', $team->id, []);
        $logs = Log::getLogs($team->id, $filters, $sort)->limit(20)->get();
        $options = LogOption::getAll($team->id,true);
        $guid_option = UserOption::get('current_option', $team->id, '');
        $option = LogOption::getByGuid($team->id, $guid_option);
        $data = [
            'title'=>'Список логов',
            'guid'=>$guid,
            'crew'=> (new CrewItemResource($team))->toArray($request),
            'logs'=>ErrorItemResource::collection($logs)->toArray($request),
            'sort'=>$sort,
            'filters'=>$filters,
            'columns'=>$columns,
            'options'=>$options,
            'option'=>$option
        ];
        return Inertia::render('Logs/LogsTeam', $data);
    }

    public function filter(Request $request): array
    {
        $team_guid = $request->get('team');
        $team = Crew::getByGuid($team_guid);
        $filters = $request->get('filter');
        $sort = $request->get('sort');
        $errors = Log::getLogs($team->id, $filters, $sort)->limit(20)->get();
        return [
            'logs'=>ErrorItemResource::collection($errors)->toArray($request)
        ];
    }

    public function optionSet(Request $request): array
    {
        $team_guid = $request->get('team');
        $team = Crew::getByGuid($team_guid);
        $filters = $request->get('filters');
        $sort = $request->get('sort');
        $columns = $request->get('columns');
        if(!is_null($sort)){
            UserOption::set('error_sort', $team->id, $sort);
            return ['result'=>true];
        }
        if(!is_null($filters)){
            UserOption::set('error_filters', $team->id, $filters);
            return ['result'=>true];
        }
        if(!is_null($columns)){
            UserOption::set('error_columns', $team->id, $columns);
            return ['result'=>true];
        }
        return ['result'=>false];
    }

    public function optionCreate(Request $request): array
    {
        $team_guid = $request->get('team');
        $team = Crew::getByGuid($team_guid);

        $name = $request->get('name');
        $filters = $request->get('filters');
        $sort = $request->get('sort');
        $columns = $request->get('columns');

        $data = $this->getData($request);
        $guid = LogOption::set($team->id, $name, $data);

        UserOption::set('current_option', $team->id, $guid);
        UserOption::set('log_sort', $team->id, $sort);
        UserOption::set('log_filters', $team->id, $filters);
        UserOption::set('log_columns', $team->id, $columns);

        $option = LogOption::getByGuid($team->id, $guid, true);
        $options = LogOption::getAll($team->id, true);

        return ['result'=>true, 'options'=>$options, 'option'=>$option];
    }

    public function optionSave(Request $request): array
    {
        $team_guid = $request->get('team');
        $team = Crew::getByGuid($team_guid);

        $guid = $request->get('guid');
        $filters = $request->get('filters');
        $sort = $request->get('sort');
        $columns = $request->get('columns');

        $data = $this->getData($request);
        LogOption::setByGuid($team->id, $guid, $data);

        UserOption::set('current_option', $team->id, $guid);
        UserOption::set('log_sort',    $team->id, $sort);
        UserOption::set('log_filters', $team->id, $filters);
        UserOption::set('log_columns', $team->id, $columns);

        $option = LogOption::getByGuid($team->id, $guid, true);
        $options = LogOption::getAll($team->id, true);

        return ['result'=>true, 'options'=>$options, 'option'=>$option];
    }

    private function getData(Request $request): array
    {
        return [
            'filters'=>$request->get('filters'),
            'sort'=>$request->get('sort'),
            'columns'=>$request->get('columns')
        ];
    }

    public function optionDelete(Request $request): array
    {
        $team_guid = $request->get('team');
        $team = Crew::getByGuid($team_guid);

        $guid = $request->get('guid');
        LogOption::removeByGuid($team->id, $guid);

        $options = LogOption::getAll($team->id);
        $option = [];
        if(count($options) > 0){
            $option = $options[0];
            UserOption::set('current_option', $team->id, $option['guid']);
        }
        $errors = $this->getLogs($team->id, $option);
        LogOption::clearData($options);
        return ['options'=>$options, 'option'=>$option, 'errors'=>$errors];
    }

    public function optionChange(Request $request): array
    {
        $team_guid = $request->get('team');
        $team = Crew::getByGuid($team_guid);

        $guid = $request->get('guid');
        $option = LogOption::getByGuid($team->id, $guid);
        if(is_null($option)){
            return [];
        }
        UserOption::set('current_option', $team->id, $guid);
        $option = LogOption::getByGuid($team->id, $guid);

        $data = $option['data'];
        $errors = $this->getLogs($team->id, $option);
        $data['errors'] = $errors;
        return $data;
    }

    private function getLogs($team, $option)
    {
        if(key_exists('data', $option)){
            $data = $option['data'];
        }else{
            $data = ['sort'=>[], 'filters'=>[], 'columns'=>[]];
        }
        UserOption::set('log_sort', $team, $data['sort']);
        UserOption::set('log_filters', $team, $data['filters']);
        UserOption::set('log_columns', $team, $data['columns']);
        return Log::getLogs($team, $data['filters'], $data['sort'])->limit(20)->get();
    }
}
