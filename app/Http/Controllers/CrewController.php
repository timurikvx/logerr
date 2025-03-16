<?php

namespace App\Http\Controllers;

use App\Models\Crew;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Crew\CrewItemResource;
use Inertia\Inertia;
use Inertia\Response;

class CrewController extends Controller
{
    public function create(Request $request): mixed
    {
        $rules = [
            'name'=>'required|string|max:255',
            'guid'=>'nullable|string|min:20|alpha_dash:ascii'
        ];

        $validator = Validator::make($request->all(), $rules, [], ['name'=>'Имя', 'guid'=>'Идентификатор']);
        $errors = $validator->errors();
        if(count($errors->all()) > 0){
            return response(['errors'=>$errors->all()], '200');
        }

        $name = $validator->getValue('name');
        $guid = $validator->getValue('guid');

        if(!Crew::check($name)){
            return response(['errors'=>['Команда с таким именем уже существует']], '200');
        }

        if(!Crew::checkGuid($guid)){
            return response(['errors'=>['Команда с таким идентификатором уже существует']], '200');
        }

        Crew::create($name, $guid);
        return ['list'=>CrewItemResource::collection(Crew::list())->toArray($request)];
    }

    public function list(Request $request): array
    {
        return ['list'=>CrewItemResource::collection(Crew::list())->toArray($request)];
    }

    public function teams(Request $request): Response
    {
        return Inertia::render('Teams/Teams');
    }

    public function team(Request $request): Response
    {
        return Inertia::render('Teams/Team');
    }

}
