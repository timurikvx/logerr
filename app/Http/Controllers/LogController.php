<?php

namespace App\Http\Controllers;

use App\Http\Resources\Errors\ErrorItemResource;
use App\Models\LogOption;
use App\Models\Log;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Actions\Paginate;

class LogController extends ListController
{

    protected string $cache_sort = 'log_sort';
    protected string $cache_filters = 'log_filters';
    protected string $cache_columns = 'log_columns';
    protected string $current_option = 'current_option';

    protected string $head = 'Список логов';

    protected string $prefix = 'log';
    protected string $OPTION = LogOption::class;

    public function apiAdd(Request $request)
    {

    }

    public function logs(Request $request): Response
    {
        $data = ['title'=>'Выбор команды логов'];
        return Inertia::render('Logs/Logs', $data);
    }

    public function getListData($team, $filters, $sort): \stdClass
    {
        $path = '/logs/'.$team->guid;
        //$query = Error::getErrors($team->id, $filters, $sort);
        $query = Log::getLogs($team->id, $filters, $sort);
        return Paginate::paginate($query, $path, ErrorItemResource::class);
    }

}
