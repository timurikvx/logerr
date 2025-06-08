<?php

namespace App\Interfaces;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

interface IListProvider
{
    function get($provider, $team, array $filters = [], array $sort = []): \stdClass;

    function list($provider, $team, string $title, Request $request): Collection;

    function updateList($provider, $team): array;

}
