<?php

namespace App\Services;

use App\Actions\Filters;
use App\Interfaces\FilterProviderInterface;
use App\Models\LogerrNames;

class FilterProvider implements FilterProviderInterface
{
    public function search($type, $field, $value): array
    {
        $collection = LogerrNames::search($type, $field, $value);
        return $collection->pluck('value')->toArray();
    }

    public function filters(): array
    {
        return Filters::equalsByTypes();
    }

}
