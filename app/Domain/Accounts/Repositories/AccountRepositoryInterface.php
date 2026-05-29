<?php

namespace App\Domain\Accounts\Repositories;

interface AccountRepositoryInterface
{
    /**
     * @return array{account_id: int, account_uuid: string}
     */
    public function create(int $userId, string $name): array;

    /**
     * @return array<int, array{account_uuid: string, name: string}>
     */
    public function findSummariesByUserId(int $userId): array;
}
