<?php

namespace App\Actions;

use App\Models\UserOption;
use Illuminate\Support\Collection;

class PageOptions
{
    public static function get(): Collection
    {
        return collect([
            'short'=>boolval(UserOption::get('short', 0, false))
        ]);
    }
}
