<?php

namespace App\Services\Teams;

use App\Interfaces\Models\NotificationInterface;
use App\Interfaces\Models\TeamInterface;
use App\Interfaces\Models\UserInterface;
use App\Interfaces\NotificationEventHandlerInterface;
use App\Interfaces\Teams\TeamMembersInterface;
use App\Models\Crew;
use App\Models\CrewMembers;
use Illuminate\Support\Enumerable;

class TeamMemberProvider implements NotificationEventHandlerInterface, TeamMembersInterface
{

    public function __construct()
    {

    }

    public function getTextInvite(TeamInterface $team): string
    {
        return 'Вы приглашены в команду '.$team->getName();
    }

    public function getTitleInvite(TeamInterface $team): string
    {
        return 'Приглашение в команду '.$team->getName();
    }

    public function getTextExclude(TeamInterface $team): string
    {
        return 'Вас исключили из команды '.$team->getName();
    }

    public function getTitleExclude(TeamInterface $team): string
    {
        return 'Исключение из команды '.$team->getName();
    }

    public function join(int $user, int $team): bool
    {
        return Crew::addToTeam($user, $team, 'manager');
    }

    public function exclude(UserInterface $user, TeamInterface $team): bool
    {
        CrewMembers::query()->where('user', '=',$user->getID())->where('crew', '=', $team->getID())->delete();
        return true;
    }

    public function changeRole(TeamInterface $team, UserInterface $user, string $role): bool
    {
        if(!in_array($role, $this->roles())){
            return false;
        }
        $member = CrewMembers::query()->where('user', '=', $user->getID())->where('crew', '=', $team->getID())->first();
        if($member == null){
            return false;
        }
        $member->roles = json_encode([$role]);
        $member->save();
        return true;
    }

    public function inviteEvent(): string
    {
        return 'InviteToTeam';
    }

    public function excludeEvent(): string
    {
        return 'ExcludeTeam';
    }

    public function changeRoleEvent(): string
    {
        return 'ChangeRoleTeam';
    }

    public function roles(): array
    {
        return Crew::roles();
    }

    public function allEvents(): array
    {
        return [
            $this->inviteEvent(),
            $this->excludeEvent(),
            $this->changeRoleEvent()
        ];
    }

    public function handleEvent(NotificationInterface $notification): bool
    {
        $type = $notification->getType();
        if($type === $this->inviteEvent()){
            return $this->join($notification->getTo(), intval($notification->getData()));
        }else if($type === $this->excludeEvent()){
            $this->exclude();
        }else if($type === $this->changeRoleEvent()){
            $this->changeRole();
        }
        return false;
    }

}
