<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogerrNames extends Model
{
    use HasFactory;

    public $timestamps = false;
    public $incrementing = false;

    public static function search($type, $field, $value): Collection
    {
        return LogerrNames::query()
            ->select('value')
            ->where('type', '=', $type)
            ->where('field', '=', $field)
            ->where('value', 'ILIKE', $value.'%')
            ->limit(20)->orderBy('value')->get();
    }

}
