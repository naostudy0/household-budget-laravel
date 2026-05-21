<?php

namespace App\Application\Auth\DTOs;

readonly class RegisterUserInput
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    ) {
    }
}
