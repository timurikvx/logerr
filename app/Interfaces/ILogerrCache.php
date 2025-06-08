<?php

namespace App\Interfaces;

interface ILogerrCache
{
    public function get(string $name, \Closure $func, int $ttl = 0): mixed;

    public function delete(string $name): void;

}
