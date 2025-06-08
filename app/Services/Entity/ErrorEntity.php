<?php

namespace App\Services\Entity;

use App\Services\WritableEntity;

class ErrorEntity extends WritableEntity
{

    public function __construct()
    {

    }

    public function filters(): array
    {
        return [];
    }

    public function sort(): array
    {
        return [];
    }

    public function columns(): array
    {
        return [];
    }

}
