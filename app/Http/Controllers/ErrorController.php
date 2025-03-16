<?php

namespace App\Http\Controllers;

use App\Http\Resources\Crew\CrewItemResource;
use App\Http\Resources\Errors\ErrorItemResource;
use App\Models\Crew;
use App\Models\Error;
use App\Models\ErrorOption;
use App\Models\UserOption;
use Illuminate\Http\Request;
use App\Actions\RabbitMQ\LogerrRabbit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class ErrorController extends Controller
{
    public function apiAdd(Request $request): mixed
    {
        if(count($request->all()) == 0){
            return response(['message'=>'Тело запроса должно быть объектом'], '400');
        }

        $rules = [
            'team' => 'required|string',
            'name' => 'required|string|max:255',
            'text' => 'required',
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

        $error = [
            'team'=>$validator->getValue('team'),
            'name'=>$validator->getValue('name'),
            'text'=>$validator->getValue('text'),
            'date'=> $validator->getValue('date'),
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
        return ['result'=>true];

    }

    public function errors(Request $request): Response
    {
        return Inertia::render('Errors');
    }

    public function errorsTeam(Request $request, $guid): Response
    {
        $team = Crew::getByGuid($guid);
        $sort = UserOption::get('error_sort', $team->id, []);
        $filters = UserOption::get('error_filters', $team->id, []);
        $columns = UserOption::get('error_columns', $team->id, []);
        $errors = Error::getErrors($team->id, $filters, $sort)->limit(20)->get();
        $options = ErrorOption::getAll($team->id,true);
        $guid_option = UserOption::get('current_option', $team->id, '');
        $option = ErrorOption::getByGuid($team->id, $guid_option);
        $data = [
            'guid'=>$guid,
            'crew'=> (new CrewItemResource($team))->toArray($request),
            'errors'=>ErrorItemResource::collection($errors)->toArray($request),
            'sort'=>$sort,
            'filters'=>$filters,
            'columns'=>$columns,
            'options'=>$options,
            'option'=>$option
        ];
        return Inertia::render('Errors/ErrorTeam', $data);
    }

    public function filter(Request $request, $guid): array
    {
        $team = Crew::getByGuid($guid);
        $filters = $request->get('filter');
        $sort = $request->get('sort');
        $errors = Error::getErrors($team->id, $filters, $sort)->limit(20)->get();
        return [
            'errors'=>ErrorItemResource::collection($errors)->toArray($request)
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
        $guid = ErrorOption::set($team->id, $name, $data);

        UserOption::set('current_option', $team->id, $guid);
        UserOption::set('error_sort', $team->id, $sort);
        UserOption::set('error_filters', $team->id, $filters);
        UserOption::set('error_columns', $team->id, $columns);

        $option = ErrorOption::getByGuid($team->id, $guid, true);
        $options = ErrorOption::getAll($team->id, true);

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
        ErrorOption::setByGuid($team->id, $guid, $data);

        UserOption::set('current_option', $team->id, $guid);
        UserOption::set('error_sort',    $team->id, $sort);
        UserOption::set('error_filters', $team->id, $filters);
        UserOption::set('error_columns', $team->id, $columns);

        $option = ErrorOption::getByGuid($team->id, $guid, true);
        $options = ErrorOption::getAll($team->id, true);

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
        ErrorOption::removeByGuid($team->id, $guid);

        $options = ErrorOption::getAll($team->id);
        $option = [];
        if(count($options) > 0){
            $option = $options[0];
            UserOption::set('current_option', $team->id, $option['guid']);
        }
        $errors = $this->getErrors($team->id, $option);
        ErrorOption::clearData($options);
        return ['options'=>$options, 'option'=>$option, 'errors'=>$errors];
    }

    public function optionChange(Request $request): array
    {
        $team_guid = $request->get('team');
        $team = Crew::getByGuid($team_guid);

        $guid = $request->get('guid');
        $option = ErrorOption::getByGuid($team->id, $guid);
        if(is_null($option)){
            return [];
        }
        UserOption::set('current_option', $team->id, $guid);
        $option = ErrorOption::getByGuid($team->id, $guid);

        $data = $option['data'];
        $errors = $this->getErrors($team->id, $option);
        $data['errors'] = $errors;
        return $data;
    }

    private function getErrors($team, $option)
    {
        if(key_exists('data', $option)){
            $data = $option['data'];
        }else{
            $data = ['sort'=>[], 'filters'=>[], 'columns'=>[]];
        }
        UserOption::set('error_sort', $team, $data['sort']);
        UserOption::set('error_filters', $team, $data['filters']);
        UserOption::set('error_columns', $team, $data['columns']);
        return Error::getErrors($team, $data['filters'], $data['sort'])->limit(20)->get();
    }

}
