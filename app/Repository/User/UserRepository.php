<?php
declare(strict_types=1);

namespace App\Repository\User;

use App\Models\User;

class UserRepository 
{
    public function __construct(
        private string $table = 'users'
    ){   
    }

    public function create(array $user): User
    {
        return User::create($user);
    }
}