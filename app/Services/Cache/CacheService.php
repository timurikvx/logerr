<?php

namespace App\Services\Cache;

use App\Interfaces\ILogerrCache;
use Illuminate\Support\Facades\Cache;

class CacheService implements ILogerrCache
{

    public function get(string $name, \Closure $func, int $ttl = 0): mixed
    {
        $value = Cache::get($name);
        if(empty($value)){
            $value = $func();
            Cache::set($name, $value, 11, $ttl);
        }
        return $value;
    }

    public function delete(string $name): void
    {
        Cache::delete($name);
    }

}
