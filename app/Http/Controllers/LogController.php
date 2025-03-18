<?php

namespace App\Http\Controllers;

use App\Http\Resources\Crew\CrewItemResource;
use App\Http\Resources\Errors\ErrorItemResource;
use App\Models\Crew;
use App\Models\ErrorOption;
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
        $options = ErrorOption::getAll($team->id,true);
        $guid_option = UserOption::get('current_option', $team->id, '');
        $option = ErrorOption::getByGuid($team->id, $guid_option);
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

}
