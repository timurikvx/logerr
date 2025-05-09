<?php

namespace App\Http\Controllers;

use App\Actions\PageOptions;
use App\Actions\RabbitMQ\LogerrRabbit;
use App\Events\HandleLogsEvent;
use App\Http\Resources\Log\LogItemResource;
use App\Models\Error;
use App\Models\LogOption;
use App\Models\Log;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;
use App\Actions\Paginate;
use Ramsey\Uuid\Uuid;

class LogController extends ListController
{

    protected string $cache_sort = 'log_sort';
    protected string $cache_filters = 'log_filters';
    protected string $cache_columns = 'log_columns';
    protected string $current_option = 'current_option';

    protected string $head = 'Список логов';

    protected string $prefix = 'log';

    protected string $title = 'Список логов';

    public function apiAdd(Request $request): mixed
    {
        if(count($request->all()) == 0){
            return response(['message'=>'Тело запроса должно быть объектом'], '400');
        }

        $rules = [
            'team' => 'required|string',
            'name' => 'required|string|max:255',
            'date' => 'nullable|date',
            'category' => 'nullable|string|max:255',
            'sub_category' => 'nullable|string|max:255',
            'sender_guid' => 'nullable|string|max:255',
            'sender_name' => 'nullable|string|max:255',
            'code' => 'nullable|integer|min:0',
            'user' => 'nullable|string|max:255',
            'device' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'version' => 'nullable|string|max:255',
            'data' => 'nullable',
            'query' => 'nullable',
            'response' => 'nullable',
        ];
        $validator = Validator::make($request->all(), $rules);
        $errors = $validator->errors();

        if(count($errors->all()) > 0){
            return response(['errors'=>$errors->all()], '400');
        }

        $guid = Uuid::uuid4()->toString();
        $date = $validator->getValue('date');
        if(empty($date)){
            $date = (new \DateTime())->modify('+3 hours')->format('Y-m-d H:i:s');
        }

        $error = [
            'team'=>$validator->getValue('team'),
            'guid'=>$guid,
            'name'=>$validator->getValue('name'),
            'text'=>$validator->getValue('text'),
            'date'=> $date,
            'category'=> $validator->getValue('category'),
            'sub_category'=> $validator->getValue('sub_category'),
            'sender_guid'=> $validator->getValue('sender_guid'),
            'sender_name'=> $validator->getValue('sender_name'),
            'code'=> $validator->getValue('code'),
            'user'=> $validator->getValue('user'),
            'device'=> $validator->getValue('device'),
            'city'=> $validator->getValue('city'),
            'region'=> $validator->getValue('region'),
            'version'=> $validator->getValue('version'),
            'data'=> $validator->getValue('data'),
            'query'=> $validator->getValue('query'),
            'response'=> $validator->getValue('response'),
        ];
        $message = json_encode(['user'=>Auth::id(), 'error'=>$error]);
        LogerrRabbit::publish($message, 'logs');
        return ['result'=>true, 'guid'=>$guid];
    }

    public function logs(Request $request): Response
    {
        $data = PageOptions::get();
        $data->put('title', 'Выбор команды просмотра логов');
        return Inertia::render('Logs/Logs', $data);
    }

    public function getListData($team, $filters, $sort): \stdClass
    {
        $query = Log::getLogs($team->id, [], []); //$filters, $sort
        return Paginate::paginate($query, $filters, $sort, LogItemResource::class, HandleLogsEvent::class);
    }

    public function columns(): array
    {
        return Log::columns();
    }

    public function filterFields(): array
    {
        return Log::filters();
    }
}
