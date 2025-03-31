<?php

namespace App\Actions;

use App\Events\HandleErrorsEvent;
use App\Models\Error;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

class Paginate
{
    public static function paginate(Builder $query, $filters, $sort, mixed $resource = null, mixed $event = null): \stdClass
    {
        $request = Route::getCurrentRequest();
        $max = 100;
        $side = 6;
        $page = intval(min($request->get('page', 1), $max));
        $limit = 50;
        $offset = ($page - 1) * $limit;

        $query_main = $query->clone();

        Filters::setFilters($query, $filters);
        Filters::setSort($query, $sort);

        $ids = $query->select(['hash'])->offset($offset)->limit($limit)->get()->pluck('hash')->toArray();
        $data_redis = collect([]);
        $from_base = [];
        foreach ($ids as $hash){
            $value = Cache::get($hash);
            if(is_null($value)){
                $from_base[] = $hash;
                continue;
            }
            $error = new Error();
            $error->fill($value);
            $data_redis[] = $error;
        }
        //dump($data_redis->count());
        $data_collect = collect([]);
        if(count($from_base) > 0){
            $data_collect = $query_main->whereIn('hash', $from_base)->get();
        }
        //dump($data_collect->count());
        $full = $data_collect->collect();
        foreach ($data_redis as $row){
            $full->push($row);
        }

        Filters::setSortCollection($full, $sort);
        if(!is_null($resource)){
            $data = $resource::collection($full)->toArray($request);
        }else{
            $data = $full;
        }

        if(!is_null($event)){
            $event::dispatch($data_collect->toArray());
        }
        //HandleErrorsEvent::dispatch($data_collect->toArray());

        $start = max($page - $side, 1);
        $end = min($start + ($side * 2) + 1, $max);
        if(count($data) < $limit){
            $end = $page + 1;
        }

        $links = array();
        for($i = $start; $i < $end; $i++){
            $links[] = ['page'=>$i,'active'=>($i === $page), 'label'=>$i]; //'url'=>$path.'?page='.$i,
        }
        $answer = new \stdClass();
        $answer->data = $data;
        $answer->paginate = [
            'prev'=>max($page - 1, 1),
            'links'=>$links,
            'current'=>$page,
            'next'=>min($page + 1, $end),
            'count'=>count($data)
        ];
        return $answer;
    }

}
