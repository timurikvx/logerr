<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface IListPreferences
{
    function columns($provider): array;

    function sort($provider): array;

    function filters($provider): array;

    //function setting($provider);

    //function current($provider, $option = null);

    //function get($provider, string|null $guid): Model|null;

    function remove($provider): void;

    function saveFilters(IListModel $provider, $data): void;

    function saveSort(IListModel $provider, $data): void;

    function saveColumns(IListModel $provider, $data): void;



}
