<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface ITeamProvider
{
    function create($name): array;

    function list(): Collection;

    function change($guid): Model|null;

}
