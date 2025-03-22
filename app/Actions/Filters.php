<?php

namespace App\Actions;

use Illuminate\Database\Eloquent\Builder;

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

    public static function columns(): array
    {
        return [
            ['class'=>'column1', 'name'=>'Дата', 'type'=>'date', 'column'=>'date', 'width'=>1],
            ['class'=>'column2', 'name'=>'Имя', 'type'=>'text', 'column'=>'name', 'width'=>1],
            ['class'=>'column3', 'name'=>'ID', 'type'=>'text', 'column'=>'guid', 'width'=>1],
            ['class'=>'column4', 'name'=>'Категория', 'type'=>'text', 'column'=>'category', 'width'=>1],
            ['class'=>'column5', 'name'=>'Подкатегория', 'type'=>'text', 'column'=>'sub_category', 'width'=>1],
            ['class'=>'column6', 'name'=>'Отправитель', 'type'=>'text', 'column'=>'sender_name', 'width'=>1],
            ['class'=>'column7', 'name'=>'Код', 'type'=>'text', 'column'=>'code', 'width'=>1],
            ['class'=>'column8', 'name'=>'Пользователь', 'type'=>'text', 'column'=>'user', 'width'=>1],
            ['class'=>'column9', 'name'=>'Устройство', 'type'=>'text', 'column'=>'device', 'width'=>1],
            ['class'=>'column10', 'name'=>'Город', 'type'=>'text', 'column'=>'city', 'width'=>1],
            ['class'=>'column11', 'name'=>'Регион', 'type'=>'text', 'column'=>'region', 'width'=>1],
            ['class'=>'column12', 'name'=>'Версия', 'type'=>'text', 'column'=>'version', 'width'=>1],
            ['class'=>'column13', 'name'=>'Длительность', 'type'=>'text', 'column'=>'duration', 'width'=>1],
        ];
    }

    public static function filters(): array
    {
        return [
            'date'=>['use'=>false, 'name'=>'Дата', 'type'=>'datetime-local', 'equal'=>null, 'value'=>null, 'value2'=>null, 'list'=>null],
            'name'=>['use'=>false, 'name'=>'Имя', 'type'=>'text', 'equal'=>null, 'value'=>null, 'value2'=>null, 'list'=>null],
            'category'=>['use'=>false, 'name'=>'Категория', 'type'=>'text', 'equal'=>null, 'value'=>null, 'value2'=>null, 'list'=>null],
            'sub_category'=>['use'=>false, 'name'=>'Подкатегория', 'type'=>'text', 'equal'=>null, 'value'=>null, 'value2'=>null, 'list'=>null],
            'user'=>['use'=>false, 'name'=>'Пользователь', 'type'=>'text', 'equal'=>null, 'value'=>null, 'value2'=>null, 'list'=>null],
            'device'=>['use'=>false, 'name'=>'Устройство', 'type'=>'text', 'equal'=>null, 'value'=>null, 'value2'=>null, 'list'=>null],
            'city'=>['use'=>false, 'name'=>'Город', 'type'=>'text', 'equal'=>null, 'value'=>null, 'value2'=>null, 'list'=>null],
            'region'=>['use'=>false, 'name'=>'Регион', 'type'=>'text', 'equal'=>null, 'value'=>null, 'value2'=>null, 'list'=>null],
            'version'=>['use'=>false, 'name'=>'Версия', 'type'=>'text', 'equal'=>null, 'value'=>null, 'value2'=>null, 'list'=>null],
            'duration'=>['use'=>false, 'name'=>'Длительность', 'type'=>'number', 'equal'=>null, 'value'=>null, 'value2'=>null, 'list'=>null],
        ];
    }

}
