<?php

namespace App\Interfaces;

use App\Interfaces\Models\TeamInterface;
use Illuminate\Support\Collection;

interface TeamServiceInterface
{

    function create($name, $guid = null): array;

    function list(): Collection;

    function getByGuid(string $guid): TeamInterface|null;

    function current(int $team = null): TeamInterface|null;

}
