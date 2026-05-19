<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Auth\Repositories\UserRepositoryInterface;
use App\Models\User;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function create(string $name, string $email, string $plainPassword): int
    {
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $plainPassword,
        ]);

        return $user->getKey();
    }
}
