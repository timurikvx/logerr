<?php

namespace App\Actions;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class Filters
{

    private static $data = [
        'equal'=>'Равно',
        'not_equal'=>'Не равно',
        'between'=>'Между',
        'more'=>'Больше',
        'more_equal'=>'Больше или равно',
        'less'=>'Меньше',
        'less_equal'=>'Меньше или равно',
        'list'=>'В списке',
        'not_list'=>'Не в списке',
        'like'=>'Содержит'
    ];

    public static function equals(): array
    {
        return [
            'date'=>['equal', 'between', 'more', 'more_equal', 'less', 'less_equal'],
            'datetime-local'=>['equal', 'between', 'more', 'more_equal', 'less', 'less_equal'],
            'time'=>['equal', 'between', 'more', 'more_equal', 'less', 'less_equal'],
            'number'=>['equal', 'not_equal', 'between', 'more', 'more_equal', 'less', 'less_equal'],
            'text'=>['equal', 'not_equal', 'list', 'not_list', 'like'],
            'boolean'=>['equal', 'not_equal']
        ];
    }

    public static function equalsByTypes(): array
    {
        $names = collect(self::$data);
        $equals = self::equals();
        $data = [];
        foreach ($equals as $key => $types){
            $list = [];
            foreach ($types as $type){
                $list[] = [
                    'name'=>$names->get($type),
                    'value'=>$type
                ];
            }
            $data[$key] = $list;
        }
        return $data;
    }

    public static function setFilters(Builder &$query, $filters): void
    {
        $filters = collect($filters);
        $keys = $filters->keys();
        foreach ($keys as $key){
            $filter = collect($filters->get($key));
            if(empty($filter)){
                continue;
            }
            $use = $filter->get('use', false);
            if(!$use){
                continue;
            }
            $equal = $filter->get('equal', 'equal');
            $value = trim($filter->get('value'));
            $value2 = $filter->get('value2');
            $list = $filter->get('list', []);
            if(empty($equal)){
                $equal = 'equal';
            }
            //dump([$value, $value2]);
            if($equal == 'equal'){
                if(empty($value)){
                    continue;
                }
                $query->where($key, '=', $value);
            }else if($equal == 'not_equal'){
                if(empty($value)){
                    continue;
                }
                $query->where($key, '<>', $value);
            }else if($equal == 'between'){
                if(empty($value) && empty($value2)){
                    continue;
                }
                $query->whereBetween($key, [$value, $value2]);
            }else if($equal == 'list'){
                if(empty($list)){
                    continue;
                }
                $query->whereIn($key, $list);
            }else if($equal == 'not_list'){
                $query->whereNotIn($key, $list);
            }else if($equal == 'more'){
                $query->where($key, '>', $value);
            }else if($equal == 'more_equal'){
                $query->where($key, '>=', $value);
            }else if($equal == 'less'){
                $query->where($key, '<', $value);
            }else if($equal == 'less_equal'){
                $query->where($key, '<=', $value);
            }else if($equal == 'like'){
                $query->where($key, 'ILIKE', $value.'%');
            }
        }
    }

    public static function setSort(Builder &$query, $sort): void
    {
        if(count($sort) === 0){
            $query->orderByDesc('date');
            return;
        }
        foreach ($sort as $item){
            $desc = $item['desc']? 'desc': 'asc';
            $query->orderBy($item['field'], $desc);
        }
    }

    public static function setSortCollection(Collection &$collection, $sort): void
    {
        if(count($sort) === 0){
            $collection = $collection
                ->sortByDesc('date')
                ->sortBy('name')
                ->sortBy('guid');
            return;
        }
        $sorts = [];
        foreach ($sort as $item){
            $desc = $item['desc']? 'desc': 'asc';
            $sorts[] = [$item['field'], $desc];
        }
        $collection->sortBy($sorts);
    }

}
