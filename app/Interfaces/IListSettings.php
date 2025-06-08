<?php

namespace App\Interfaces;

use App\Interfaces\IListModel;
use Illuminate\Http\Request;

interface IListSettings
{
    function current(IListModel $provider, $option = null);

    function changeSettings(IListModel $provider, $team, $guid): void;

    function createSetting(IListModel $provider, string $name, $team, array $data): array;

    function removeSetting(IListModel $provider, string $guid): void;

    function changeSetting();

    function saveSetting(IListModel $provider, $guid, $data): array;

    function getSettingData(Request $request): array;

}
