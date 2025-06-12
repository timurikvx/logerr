<?php

namespace App\Services\Teams;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class TeamValidator
{

    public function __construct()
    {

    }

    public function validateInvite($team, $inviter, $user, $type)
    {
        $data = [
            'try'=>Cache::get('invite_try_'.$inviter->id(), 0),
            'team'=>$team,
            'user'=>$user,
            'iam'=>($inviter->id === $user->id),
            'type'=>$type,
        ];
        $rules = [
            'try' => ['required', 'integer', 'max:5'],
            'team' => ['required'],
            'user' => ['required'],
            'iam' => ['in:false'],
            'type'=>['required', 'string'],
        ];
        $messages = [
            'team' => 'Команда не найдена',
            'user' => 'Пользователь не найден',
            'iam' => 'Вы приглашаете самого себя',
            'try' => 'Слишком много неудачных приглашений. Подождите 2 минуты перед следующей попыткой',
        ];
        $validator = Validator::make($data, $rules, $messages);
        $errors = $validator->errors();
        dump($errors);
        if(count($errors->all()) > 0){
            return response(['errors'=>$errors->all()], '400');
        }
    }

}
