<?php

namespace App\Services\UserOptions;

use App\Interfaces\LogerrCacheInterface;
use App\Interfaces\UserSettingsServiceInterface;
use App\Models\UserOption;

class UserSettingsService implements UserSettingsServiceInterface
{

    public function __construct(LogerrCacheInterface $cache)
    {
        $this->cache = $cache;
    }
    public function set(string $name, $team, mixed $value): void
    {
        UserOption::set($name, $team, $value);
    }

    public function get(string $name, int $team = 0, mixed $default = null): mixed
    {
        //return $this->cache->get($name.'_'.$team, function () use ($name, $team, $default){
        return UserOption::get($name, $team, $default);
        //});
    }

    public function remove(string $name, int $team): void
    {
        //$this->cache->delete($name);
        UserOption::remove($name, $team);
    }
}
