<?php

namespace App\Interfaces;

interface FilterProviderInterface
{
    function search($type, $field, $value): array;

    function filters(): array;

}
