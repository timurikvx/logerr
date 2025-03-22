<?php

namespace App\Actions;

use App\Http\Resources\Errors\ErrorItemResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;

class Paginate
{
    public static function paginate(Builder $query, mixed $resource = null): \stdClass
    {
        $request = Route::getCurrentRequest();
        $max = 50;
        $side = 6;
        $page = intval(min($request->get('page', 1), $max));
        $limit = 50;
        $offset = ($page - 1) * $limit;

        $data = $query->offset($offset)->limit($limit)->get();
        if(!is_null($resource)){
            $data = $resource::collection($data)->toArray($request);
        }
        $base = $request->fullUrlWithoutQuery(['page']);

        $start = max($page - $side, 1);
        $end = $start + ($side * 2) + 1;
        $links = array();
        for($i = $start; $i < $end; $i++){
            $links[] = ['url'=>$base.'?page='.$i, 'active'=>($i === $page), 'label'=>$i];
        }

        $answer = new \stdClass();
        $answer->data = $data;
        $answer->paginate = [
            'prev'=>max($page - 1, 1),
            'links'=>$links,
            'current'=>$page,
            'next'=>$page + 1
        ];
        return $answer;
    }

}
