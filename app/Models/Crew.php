<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;

class Crew extends Model
{
    use HasFactory;

    public static function create($name, $guid = null):void
    {
        $user = Auth::id();
        if($guid === null){
            $guid = Uuid::uuid4()->toString();
        }

        $crew = new Crew();
        $crew->name = $name;
        $crew->creator = $user;
        $crew->guid = $guid;
        $crew->save();

        $crewMember = new CrewMembers();
        $crewMember->user = $user;
        $crewMember->crew = $crew->id;
        $crewMember->roles = json_encode(['admin']);
        $crewMember->save();
    }

    public static function check($name):bool
    {
        $user = Auth::id();
        $ids = CrewMembers::query()->select(['crew'])->where('user', '=', $user)->get()->pluck('crew');
        $query = self::query()->whereIn('id', $ids)->where('name', '=', $name);
        $check = $query->first();
        return ($check === null);
    }

    public static function checkGuid($guid):bool
    {
        $user = Auth::id();
        $ids = CrewMembers::query()->select(['crew'])->where('user', '=', $user)->get()->pluck('crew');
        $query = self::query()->whereIn('id', $ids)->where('guid', '=', $guid);
        $check = $query->first();
        return ($check === null);
    }

    public static function list(): Collection
    {
        $user = Auth::id();
        $ids = CrewMembers::query()->select(['crew', 'roles'])->where('user', '=', $user)->get();
        $roles = $ids->pluck('roles', 'crew');
        $list = self::query()->whereIn('id', $ids->pluck('crew'))->orderBy('name')->get();
        foreach ($list as $item){
            $item->roles = json_decode($roles->get($item->id));
        }
        return $list;
    }

    public static function getByGuid($guid): Model|null
    {
        $user = Auth::id();
        $ids = CrewMembers::query()->select(['crew', 'roles'])->where('user', '=', $user)->get();
        $roles = $ids->pluck('roles', 'crew');
        $crew = self::query()->whereIn('id', $ids->pluck('crew'))->where('guid', '=', $guid)->first();
        if(is_null($crew)){
            return null;
        }
        $crew->roles = json_decode($roles->get($crew->id));
        return $crew;
    }

    public static function addToTeam($user_id, $team_id, $role = null): void
    {
        $role = ($role === null)? 'user': $role;
        $member = new CrewMembers();
        $member->user = $user_id;
        $member->crew = $team_id;
        $member->roles = json_encode([$role]);
        $member->save();
    }

    public static function roles(): array
    {
        return [
            'admin'=>'Администратор',
            'manager'=>'Управляющий',
            'user'=>'Пользователь'
        ];
    }

    public static function getMembers($team): Collection
    {
        $ids = CrewMembers::query()->select(['user', 'roles'])->where('crew', '=', $team)->get();
        $roles = $ids->pluck('roles', 'user');
        $users = User::query()->whereIn('id', $ids->pluck('user'))->orderBy('surname')->orderBy('name')->get();
        $list = collect([]);
        foreach ($users as $user){
            $list[] = ['user'=>$user, 'roles'=>json_decode($roles->get($user->id))];
        }
        return $list;
    }

}
