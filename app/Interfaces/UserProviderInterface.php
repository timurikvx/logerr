<?php

namespace App\Interfaces;

use App\Interfaces\Models\UserInterface;

interface UserProviderInterface
{

    function getByEmail(string $email): UserInterface|null;

}
