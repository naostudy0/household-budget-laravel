<?php

namespace App\Application\Accounts\DTOs;

readonly class CreateAccountResult
{
    public function __construct(
        public int $accountId,
        public string $accountUuid,
    ) {
    }
}
