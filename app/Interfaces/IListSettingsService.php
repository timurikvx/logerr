<?php

namespace App\Interfaces;

use App\Models\Option;
use Illuminate\Database\Eloquent\Model;

interface IListSettingsService
{

    function current($team, $prefix): array|null;

    public function set(int $team, string $name, mixed $value, $category = null): string;

    function get(int $team, string|null $guid, $category = null): Model|null;

    public static function setByGuid($team, $guid, $value, $category = null): string|null;

    public static function getByGuid($team, $guid, $category = null, $without_data = false): mixed;

    public static function updateByGuid($team, $guid, $value, $category = null): string|null;

    public static function getAll($team, $category, $without_data = false): array;

    public static function remove($team, $name, $category = null): void;

    public static function removeByGuid($team, $guid): void;

    public static function clearData(&$options): void;

}
