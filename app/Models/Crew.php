<?php

namespace App\Models;

use App\Http\Resources\Crew\CrewItemResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class Crew extends Model
{
    use HasFactory;

    public static function create($name, $url):void
    {
        $user = Auth::id();

        $crew = new Crew();
        $crew->name = $name;
        $crew->creator = $user;
        $crew->save();

        $crewMember = new CrewMembers();
        $crewMember->user = $user;
        $crewMember->crew = $crew->id;
        $crewMember->save();
    }

    public static function list(): Collection
    {
        $user = Auth::id();
        $ids = CrewMembers::query()->select(['crew'])->where('user', '=', $user)->get()->pluck('crew');
        return self::query()->whereIn('id', $ids)->get();
    }

}
