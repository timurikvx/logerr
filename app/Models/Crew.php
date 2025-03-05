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
        $ids = CrewMembers::query()->select(['crew'])->where('user', '=', $user)->get()->pluck('crew');
        return self::query()->whereIn('id', $ids)->get();
    }

    public static function getByGuid($guid): Model|null
    {
        $user = Auth::id();
        $ids = CrewMembers::query()->select(['crew'])->where('user', '=', $user)->get()->pluck('crew');
        $query = self::query()->whereIn('id', $ids->toArray())->where('guid', '=', $guid);
        return $query->first();
    }

}
