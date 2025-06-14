<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface ListSettingsInterface
{
    function current(ListModelInterface $provider, $option = null);

    function changeSettings(ListModelInterface $provider, $team, $guid): void;

    function createSetting(ListModelInterface $provider, string $name, $team, array $data): array;

    function removeSetting(ListModelInterface $provider, string $guid): void;

    function saveSetting(ListModelInterface $provider, $guid, $data): array;

    function getSettingData(Request $request): array;

    function removeConditions(ListModelInterface $provider, $team): void;

    function getFilters($provider): array;

    function getSort($provider): array;

    function getColumns($provider): array;

    function saveFilters(ListModelInterface $provider, $data): void;

    function saveSort(ListModelInterface $provider, $data): void;

    function saveColumns(ListModelInterface $provider, $data): void;

    function clearCondition(ListModelInterface $provider, $field): void;

}
