<?php

namespace App\Interfaces;

interface WritableEntityInterface
{

    function columns(): array;

    function filters(): array;

    function sort(): array;

}
