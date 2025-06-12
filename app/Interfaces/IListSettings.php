<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface IListSettings
{
    function current(IListModel $provider, $option = null);

    function changeSettings(IListModel $provider, $team, $guid): void;

    function createSetting(IListModel $provider, string $name, $team, array $data): array;

    function removeSetting(IListModel $provider, string $guid): void;

    function saveSetting(IListModel $provider, $guid, $data): array;

    function getSettingData(Request $request): array;

    function removeConditions(IListModel $provider, $team): void;

    function getFilters($provider): array;

    function getSort($provider): array;

    function getColumns($provider): array;

    function saveFilters(IListModel $provider, $data): void;

    function saveSort(IListModel $provider, $data): void;

    function saveColumns(IListModel $provider, $data): void;

    function clearCondition(IListModel $provider, $field): void;

}
