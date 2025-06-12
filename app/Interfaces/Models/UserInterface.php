<?php

namespace App\Interfaces\Models;

interface UserInterface
{
    
    function getID(): int;

    function getName(): string;

    function getSurname(): string;

    function getEmail(): string;

    function getBirth(): \DateTime;

}
