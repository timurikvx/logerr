<?php

namespace App\Interfaces;

use Illuminate\Support\Collection;

interface NotificationInterface
{

    function get(): Collection;

    function complete($guid): bool;

}
