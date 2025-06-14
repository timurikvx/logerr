<?php

namespace App\Interfaces\Models;

interface TeamInterface
{
    function getID(): int;

    function getName(): string;

    public function getRoles(UserInterface $user): array;

    //public function save(): bool;

}
