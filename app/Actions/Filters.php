<?php

namespace App\Actions;

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

}
