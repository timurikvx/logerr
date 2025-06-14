<?php

namespace App\Services;

use App\Interfaces\UserProviderInterface;
use App\Interfaces\Models\UserInterface;

class UserProvider implements UserProviderInterface
{

    public function __construct(UserInterface $userFactory)
    {
        $this->userFactory = $userFactory;
    }

    public function getByEmail(string $email): UserInterface|null
    {
        return $this->userFactory->getByEmail($email);
    }
}
