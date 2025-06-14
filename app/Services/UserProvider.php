<?php

namespace App\Services;

use App\Interfaces\IUserProvider;
use App\Interfaces\Models\UserInterface;

class UserProvider implements IUserProvider
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
