<?php

namespace App\Services;

use App\Interfaces\TeamProviderInterface;
use App\Interfaces\TeamServiceInterface;
use App\Interfaces\Models\TeamInterface;
use App\Interfaces\Models\UserInterface;
use App\Models\Crew;
use App\Models\CrewMembers;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Enumerable;

class TeamProvider implements TeamProviderInterface
{

    public function __construct(TeamServiceInterface $teamService)
    {
        $this->teamService = $teamService;
    }

    public function current($guid = null): TeamInterface|null
    {
        if(!is_null($guid)){
            return $this->change($guid);
        }
        return $this->teamService->current();
    }

    public function change($guid): TeamInterface|null
    {
        $team = $this->teamService->getByGuid($guid);
        if($team == null){
            return null;
        }
        return $this->teamService->current($team->id);
    }

    public function members($team): Enumerable
    {
        return Crew::getMembers($team->id);
    }

    public function list(): Enumerable
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

    public function get(string $guid): TeamInterface|null
    {
        return Crew::getByGuid($guid);
    }

    public function getByID(int $id): TeamInterface|null
    {
        return Crew::find($id);
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

    public function changeRole(TeamInterface $team, UserInterface $user, $role): bool
    {
        $member = CrewMembers::query()->where('user', '=',$user->getID())->where('crew', '=', $team->getID())->first();
        if($member == null){
            return false;
        }
        $member->roles = json_encode([$role]);
        $member->save();
        return true;
    }

    public function exclude(UserInterface $user, TeamInterface $team): Enumerable
    {
        CrewMembers::query()->where('user', '=',$user->getID())->where('crew', '=', $team->getID())->delete();
        return Crew::getMembers($team->getID());
    }

    public function listWithout(Enumerable $teams): Enumerable
    {
        $list = [];
        $ids = $teams->pluck('id')->values()->toArray();
        $all = $this->teamService->list();
        foreach ($all as $team){
            if(in_array($team->getID(), $ids)){
                continue;
            }
            $list[] = $team;
        }
        return collect($list);
    }

}
