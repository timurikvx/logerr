<?php

namespace App\Services;

use App\Interfaces\IWritableEntity;

abstract class WritableEntity implements IWritableEntity
{

    public string $cache_sort = 'error_sort';
    public string $cache_filters = 'error_filters';
    public string $cache_columns = 'error_columns';

    public function columns(): array
    {
        return [];
    }

    public function sort(): array
    {
        return [];
    }

    public function filters(): array
    {
        return [];
    }

}
