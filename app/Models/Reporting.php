<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;


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
        $result = $report->save();
        if($result){
            Cache::set('report_'.$team.$name.$category, $value);
        }
        return $result;
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
        return 'string';
    }

    private static function getValue($type, $value): float|bool|int|string
    {
        if($type === 'bool'){
            return boolval($value);
        }
        if($type === 'float'){
            return floatval($value);
        }
        if($type === 'int'){
            return intval($value);
        }
        if($type === 'double'){
            return doubleval($value);
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

    public static function getByTeam($team, $category = null, $name = null): mixed
    {
        if(is_array($team)){
            $query = self::query()->whereIn('team', $team);
        }else{
            $query = self::query()->where('team', '=', $team);
        }
        if($category != null){
            $query->where('category', '=', $category);
        }
        if($name != null){
            $query->where('name', '=', $name);
        }
        $data = $query->select(['name', 'team', 'category', 'type', 'value'])->get();
        $list = collect([]);
        foreach ($data as $row){
            $value = self::getValue($row->type, $row->value);
            $list->push(['name'=>$row->name, 'team'=>$row->team, 'value'=>$value]);
        }
        return $list;
    }

}
