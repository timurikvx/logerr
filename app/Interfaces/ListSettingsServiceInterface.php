<?php

namespace App\Interfaces;

use App\Models\Option;
use Illuminate\Database\Eloquent\Model;

interface ListSettingsServiceInterface
{

    function current($team, $prefix): array|null;

    public function set(int $team, string $name, mixed $value, $category = null): string;

    function get(int $team, string|null $guid, $category = null): Model|null;

    public function setByGuid($team, $guid, $value, $category = null): string|null;

    public function getByGuid($team, $guid, $category = null, $without_data = false): mixed;

    public function updateByGuid($team, $guid, $value, $category = null): string|null;

    public function getAll($team, $category, $without_data = false): array;

    //public function remove($team, $name, $category = null): void;

    public function removeByGuid($team, $guid): void;

    //public function clearData(&$options): void;

}
