<?php

namespace App\Http\Controllers;

use App\Actions\Filters;
use App\Http\Resources\Crew\CrewItemResource;
use App\Http\Resources\Errors\ErrorItemResource;
use App\Models\Crew;
use App\Models\Error;
use Illuminate\Http\Request;
use App\Actions\RabbitMQ\LogerrRabbit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ValidatedInput;
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
        $crew = Crew::getByGuid($guid);
        $errors = Error::getErrors($guid)->orderByDesc('date')->limit(20)->get();
        $data = [
            'guid'=>$guid,
            'crew'=> (new CrewItemResource($crew))->toArray($request),
            'errors'=>ErrorItemResource::collection($errors)->toArray($request)
        ];
        return Inertia::render('Errors/ErrorTeam', $data);
    }

    public function filter(Request $request, $guid)
    {
        $query = Error::getErrors($guid)->orderByDesc('date')->limit(20);
        $filters = $request->get('filter');
        $sort = $request->get('sort');
        Filters::setFilters($query, $filters);
        $errors = $query->get();
        return [
            'errors'=>ErrorItemResource::collection($errors)->toArray($request)
        ];

        //dump($request->all());
    }


}
