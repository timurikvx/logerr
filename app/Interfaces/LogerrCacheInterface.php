<?php

namespace App\Interfaces;

interface LogerrCacheInterface
{
    public function get(string $name, \Closure $func, int $ttl = 0): mixed;

    public function delete(string $name): void;

}
