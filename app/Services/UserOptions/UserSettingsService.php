<?php

namespace App\Services\UserOptions;

use App\Interfaces\ILogerrCache;
use App\Interfaces\IUserSettingsService;
use App\Models\UserOption;

class UserSettingsService implements IUserSettingsService
{

    public function __construct(ILogerrCache $cache)
    {
        $this->cache = $cache;
    }
    public function set(string $name, $team, mixed $value): void
    {
        UserOption::set($name, $team, $value);
    }

    public function get(string $name, int $team = 0, mixed $default = null): mixed
    {
        //return $this->cache->get($name, function () use ($name, $team, $default){
        return UserOption::get($name, $team, $default);
        //});
    }

    public function remove(string $name, int $team): void
    {
        //$this->cache->delete($name);
        UserOption::remove($name, $team);
    }
}
