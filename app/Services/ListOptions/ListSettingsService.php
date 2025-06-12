<?php

namespace App\Services\ListOptions;

use App\Interfaces\IListSettingsService;
use App\Interfaces\ILogerrCache;
use App\Interfaces\IUserSettingsService;
use App\Models\Option;
use Illuminate\Database\Eloquent\Model;

class ListSettingsService implements IListSettingsService
{

    public function __construct(IUserSettingsService $userOption, ILogerrCache $cache)
    {
        $this->userOption = $userOption;
        $this->cache = $cache;
    }

    public function current($team, $prefix): array|null
    {
        $guid = $this->userOption->get($prefix.'_current_option', $team->id);
        return Option::getByGuid($team->id, $guid, 'error');
    }

    public function get(int $team, string|null $guid, $category = null): Model|null
    {
        return Option::get($team, $guid, $category);
    }

    public function set(int $team, string $name, $value, $category = null): string
    {
        return Option::set($team, $name, $value, $category);
    }

    public function setByGuid($team, $guid, $value, $category = null): string|null
    {
        return Option::setByGuid($team, $guid, $value, $category);
    }

    public function getByGuid($team, $guid, $category = null, $without_data = false): mixed
    {
        return Option::getByGuid($team->id, $guid, $category, $without_data);
    }

    public function updateByGuid($team, $guid, $value, $category = null): string|null
    {
        return Option::updateByGuid($team, $guid, $value, $category);
    }

    public function getAll($team, $category, $without_data = false): array
    {
        return Option::getAll($team, $category, $without_data);
    }

//    public function remove($team, $name, $category = null): void
//    {
//        Option::remove($team, $name, $category);
//    }

    public function removeByGuid($team, $guid): void
    {
        Option::removeByGuid($team, $guid);
    }

//    public function clearData(&$options): void
//    {
//        Option::clearData($options);
//    }

}
