<?php

namespace App\Interfaces;

interface ListModelInterface
{
    function availableColumns(): array;

    function cacheSort(): string;

    function cacheFilters(): string;

    function cacheColumns(): string;

    function prefix(): string;

    function getFilters(): array;

    function add(array $data, $team, $user = null): bool;

}
