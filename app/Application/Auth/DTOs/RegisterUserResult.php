<?php

namespace App\Application\Auth\DTOs;

readonly class RegisterUserResult
{
    public function __construct(
        public int $userId,
    ) {
    }
}
