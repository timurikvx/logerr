<?php

namespace App\Interfaces;

interface IListModel
{
    function availableColumns(): array;

    function cacheSort(): string;

    function cacheFilters(): string;

    function cacheColumns(): string;

    function prefix(): string;

    function getFilters(): array;

}
