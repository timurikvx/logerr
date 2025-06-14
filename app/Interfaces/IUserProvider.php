<?php

namespace App\Interfaces;

use App\Interfaces\Models\UserInterface;

interface IUserProvider
{

    function getByEmail(string $email): UserInterface|null;

}
