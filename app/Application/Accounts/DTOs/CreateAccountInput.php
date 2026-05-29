<?php

namespace App\Application\Accounts\DTOs;

readonly class CreateAccountInput
{
    public function __construct(
        public int $userId,
        public string $name,
    ) {
    }
}
