<?php

namespace App\Http\Controllers;

use App\Models\Crew;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Crew\CrewItemResource;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Cache;

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
        $data = ['title'=>'Управление командами'];
        return Inertia::render('Teams/Teams', $data);
    }

    public function team(Request $request): Response
    {
        $data = ['title'=>'Команда'];
        return Inertia::render('Teams/Team', $data);
    }

    public function invite(Request $request)
    {
        $iam = Auth::id();
        $team_guid = $request->get('guid');
        $email = $request->get('email');

        $team = Crew::getByGuid($team_guid);
        $user = User::query()->where('email', '=', $email)->first();
        if(is_null($team)){
            return ['error'=>'Команда не найдена'];
        }
        $try = Cache::get('invite_try_'.$iam, 0);
        if($try >= 5){
            return ['error'=>'Слишком много неудачных приглашений. Подождите 2 минуты перед следующей попыткой', 'try'=>$try];
        }
        if(is_null($user)){
            Cache::set('invite_try_'.$iam, $try + 1, 120);
            return ['error'=>'Пользователь не найден', 'try'=>$try];
        }
    }

}
