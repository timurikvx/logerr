<?php

namespace App\Interfaces;

interface IWritableEntity
{

    function columns(): array;

    function filters(): array;

    function sort(): array;

}
