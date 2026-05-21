<?php

namespace App\Domain\Auth\Repositories;

interface UserRepositoryInterface
{
    public function create(string $name, string $email, string $plainPassword): int;
}
