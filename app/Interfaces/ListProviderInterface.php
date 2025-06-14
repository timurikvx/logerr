<?php

namespace App\Interfaces;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

interface ListProviderInterface
{
    function get($provider, $team, array $filters = [], array $sort = []): \stdClass;

    function list($provider, $team, Request $request): Collection;

    function updateList($provider, $team): array;

    function saveFilters($provider, $team, $filters, $sort): \stdClass;

}
