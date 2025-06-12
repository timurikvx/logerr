<?php

namespace App\Interfaces;

interface IUserProvider
{

    function getByEmail(string $email);

}
