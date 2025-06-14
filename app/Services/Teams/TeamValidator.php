<?php

namespace App\Services\Teams;

use App\Models\Notification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class TeamValidator
{

    public function __construct()
    {

    }

    public function validateInvite($team, $inviter, $user, $type): array
    {

        $errors = [];
        if(is_null($user)){
            $errors[] = 'Пользователь с указанной почтой не найден';
        }
        if(is_null($team)){
            $errors[] = 'Команда не найдена';
        }
        if($inviter->getID() == $user->getID()){
            $errors[] = 'Вы приглашаете самого себя';
        }
//        if(Notification::exist($type, $user->id)){
//            Cache::set('invite_try_'.$inviter->getID(), $try + 1, 120);
//            return ['error'=>'Вы приглашаете самого себя', 'try'=>$try];
//        }
        if(count($errors) > 0){
            return $errors;
        }
        $try = Cache::get('invite_try_'.$inviter->getID(), 0);
        $data = [
            'try'=>$try,
            'team'=>$team,
            'user'=>$user,
            //'type'=>$type,
        ];
        $rules = [
            'try' => ['required', 'integer', 'max:5'],
            'team' => ['required'],
            'user' => ['required'],
            //'type'=>['required', 'string'],
        ];
        $messages = [
            'try' => 'Слишком много неудачных приглашений. Подождите 2 минуты перед следующей попыткой',
            'team' => 'Команда не найдена',
            'user' => 'Пользователь не найден',
        ];
        $validator = Validator::make($data, $rules, $messages);
        $errors = $validator->errors();
        Cache::set('invite_try_'.$inviter->getID(), $try + 1, 120);
        return $errors->all();
    }

}
