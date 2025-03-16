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
        $sort = UserOption::get('error_sort', []);
        $filters = UserOption::get('error_filters', []);
        $columns = UserOption::get('error_columns', []);
        $crew = Crew::getByGuid($guid);
        $errors = Error::getErrors($guid, $filters, $sort)->limit(20)->get();
        $options = ErrorOption::getAll(true);
        $guid_option = UserOption::get('current_option', '');
        $option = ErrorOption::getByGuid($guid_option);
        $data = [
            'guid'=>$guid,
            'crew'=> (new CrewItemResource($crew))->toArray($request),
            'errors'=>ErrorItemResource::collection($errors)->toArray($request),
            'sort'=>$sort,
            'filters'=>$filters,
            'columns'=>$columns,
            'options'=>$options,
            'option'=>$option
        ];
        return Inertia::render('Errors/ErrorTeam', $data);
    }

    public function filter(Request $request, $guid)
    {
        $filters = $request->get('filter');
        $sort = $request->get('sort');
        $errors = Error::getErrors($guid, $filters, $sort)->limit(20)->get();
        return [
            'errors'=>ErrorItemResource::collection($errors)->toArray($request)
        ];
    }

    public function optionSet(Request $request)
    {
        $filters = $request->get('filters');
        $sort = $request->get('sort');
        $columns = $request->get('columns');
        if(!is_null($sort)){
            UserOption::set('error_sort', $sort);
            return ['result'=>true];
        }
        if(!is_null($filters)){
            UserOption::set('error_filters', $filters);
            return ['result'=>true];
        }
        if(!is_null($columns)){
            UserOption::set('error_columns', $columns);
            return ['result'=>true];
        }
        return ['result'=>false];
    }

    public function optionSave(Request $request): array
    {
        $name = $request->get('name');
        $filters = $request->get('filters');
        $sort = $request->get('sort');
        $columns = $request->get('columns');
        $data = [
            'filters'=>$filters,
            'sort'=>$sort,
            'columns'=>$columns
        ];
        $guid = ErrorOption::set($name, $data);
        UserOption::set('current_option', $guid);

        UserOption::set('error_sort', $sort);
        UserOption::set('error_filters', $filters);
        UserOption::set('error_columns', $columns);

        $option = ErrorOption::getByGuid($guid, true);
        $options = ErrorOption::getAll(true);
        return ['result'=>true, 'options'=>$options, 'option'=>$option];
    }

    public function optionDelete(Request $request): array
    {
        $team = $request->get('team');
        $guid = $request->get('guid');
        ErrorOption::removeByGuid($guid);

        $options = ErrorOption::getAll();
        $option = [];
        if(count($options) > 0){
            $option = $options[0];
            UserOption::set('current_option', $option['guid']);
        }
        $errors = $this->getErrors($team, $option);
        ErrorOption::clearData($options);
        return ['options'=>$options, 'option'=>$option, 'errors'=>$errors];
    }

    public function optionChange(Request $request): array
    {
        $team = $request->get('team');
        $guid = $request->get('guid');
        $option = ErrorOption::getByGuid($guid);
        if(is_null($option)){
            return [];
        }
        UserOption::set('current_option', $guid);
        $option = ErrorOption::getByGuid($guid);

        $data = $option['data'];
        $errors = $this->getErrors($team, $option);
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
        UserOption::set('error_sort', $data['sort']);
        UserOption::set('error_filters', $data['filters']);
        UserOption::set('error_columns', $data['columns']);
        return Error::getErrors($team, $data['filters'], $data['sort'])->limit(20)->get();
    }

}
