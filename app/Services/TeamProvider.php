<?php

namespace App\Services;

use App\Interfaces\ITeamProvider;
use App\Interfaces\ITeamService;
use App\Models\Crew;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class TeamProvider implements ITeamProvider
{

    public function __construct(ITeamService $teamService)
    {
        $this->teamService = $teamService;
    }

    public function current($guid = null): Model|null
    {
        if(!is_null($guid)){
            return $this->change($guid);
        }
        return $this->teamService->current();
    }

    public function change($guid): Model|null
    {
        $team = $this->teamService->getByGuid($guid);
        if($team == null){
            return null;
        }
        return $this->teamService->current($team->id);
    }

    public function members($team): Collection
    {
        return Crew::getMembers($team->id);
    }

    public function list(): Collection
    {
        return $this->teamService->list();
    }

    public function create($name): array
    {
        return $this->teamService->create($name);
    }

    public function rename(&$team, $name): void
    {
        Crew::rename($team, $name);
    }

    public function get(string $guid): Model|null
    {
        return Crew::getByGuid($guid);
    }

    public function invite($inviter, string $email, string $team_guid): array
    {
        $team = $this->teamService->getByGuid($team_guid);
        $user = User::getUser($email);
        $try = Cache::get('invite_try_'.$inviter->id(), 0);

        if(is_null($team)){
            return ['error'=>'Команда не найдена'];
        }
        if($try >= 5){
            return ['error'=>'Слишком много неудачных приглашений. Подождите 2 минуты перед следующей попыткой', 'try'=>$try];
        }
        if(is_null($user)){
            Cache::set('invite_try_'.$inviter->id(), $try + 1, 120);
            return ['error'=>'Пользователь не найден', 'try'=>$try];
        }
        if($inviter->id === $user->id){
            Cache::set('invite_try_'.$inviter->id(), $try + 1, 120);
            return ['error'=>'Вы приглашаете самого себя', 'try'=>$try];
        }
        $type = 'invite_to_team';
        if(Notification::exist($type, $user->id)){
            Cache::set('invite_try_'.$inviter->id(), $try + 1, 120);
            return ['error'=>'Вы приглашаете самого себя', 'try'=>$try];
        }
        $text = 'Вы приглашены в команду '.$team->name.' вступите или проигнорируйте уведомление!';
        Notification::create($type, $user->id, 'Приглашение в команду '.$team->name, $text, $team->toArray());
        return ['result'=>true];
    }

}
