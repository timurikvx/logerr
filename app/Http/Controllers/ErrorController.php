<?php

namespace App\Http\Controllers;

use App\Actions\PageOptions;
use App\Actions\Paginate;
use App\Http\Resources\Crew\CrewItemResource;
use App\Http\Resources\Errors\ErrorItemResource;
use App\Models\Crew;
use App\Models\Error;
use App\Models\ErrorOption;
use Illuminate\Http\Request;
use App\Actions\RabbitMQ\LogerrRabbit;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;
use App\Events\HandleErrorsEvent;
use Ramsey\Uuid\Uuid;

class ErrorController extends ListController
{

    protected string $cache_sort = 'error_sort';
    protected string $cache_filters = 'error_filters';
    protected string $cache_columns = 'error_columns';
    protected string $current_option = 'current_option';

    protected string $head = 'Список ошибок';

    protected string $prefix = 'error';
    //protected string $OPTION = ErrorOption::class;

    protected string $title = 'Список ошибок';

    public function apiAdd(Request|Collection $request): mixed
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
        ];
        $message = json_encode(['user'=>Auth::id(), 'error'=>$error]);
        LogerrRabbit::publish($message, 'errors');
        return ['result'=>true, 'guid'=>$guid];

    }

    public function errors(Request $request): Response
    {
        $data = PageOptions::get();
        $data->put('title', 'Выбор команды ошибок');
        return Inertia::render('Errors/Errors', $data);
    }

    public function getListData($team, $filters, $sort): \stdClass
    {
        $query = Error::getErrors($team->id, [], []); //$filters, $sort
        return Paginate::paginate($query, $filters, $sort, ErrorItemResource::class, HandleErrorsEvent::class);
    }

    public function selectTeam(Request $request): Response
    {
        $data = PageOptions::get();
        $data->put('title', 'Выбор команды ошибок');
        $data->put('teams', CrewItemResource::collection(Crew::list())->toArray($request));
        return Inertia::render('Teams/SelectTeamError', $data);
    }

}
