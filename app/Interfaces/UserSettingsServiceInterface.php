<?php

namespace App\Interfaces;

interface UserSettingsServiceInterface
{
    function set(string $name, $team, mixed $value): void;

    function get(string $name, int $team = 0, mixed $default = null): mixed;

    function remove(string $name, int $team): void;

}
