<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use function Laravel\Prompts\select;

class Reporting extends Model
{
    use HasFactory;

    public $timestamps = false;
    public $incrementing = false;

    public static function write($team, $name, $category = null, $value = null): bool
    {
        self::clear($team, $name, $category);
        $report = new Reporting();
        $report->name = $name;
        $report->team = $team;
        $report->category = $category;
        $report->type = self::type($value);
        $report->value = $value;
        return $report->save();
    }

    private static function type($value): string
    {
        if(is_bool($value)){
            return 'bool';
        }
        if(is_float($value)){
            return 'float';
        }
        if(is_int($value)){
            return 'int';
        }
        if(is_double($value)){
            return 'double';
        }
        if(is_numeric($value)){
            return 'numeric';
        }
        return 'string';
    }

    public static function exist($team, $name, $category = null): bool
    {
        $record = Reporting::query()
            ->where('team', '=', $team)
            ->where('name', '=', $name)
            ->where('category', '=', $category)
            ->first();
        return $record != null;
    }

    public static function clear($team, $name, $category = null): bool
    {
        return Reporting::query()
            ->where('team', '=', $team)
            ->where('name', '=', $name)
            ->where('category', '=', $category)
            ->delete();
    }

    public static function getByTeam($team, $category = null, $name = null): Collection
    {
        $query = self::query()->where('team', '=', $team);
        if($category != null){
            $query->where('category', '=', $category);
        }
        if($name != null){
            $query->where('name', '=', $name);
        }
        return $query->get();
    }

}
