<?php

namespace App\Application\Accounts\Queries;

use App\Domain\Accounts\Repositories\AccountRepositoryInterface;
use Illuminate\Support\Collection;

class AccountIndexQuery
{
    public function __construct(
        private readonly AccountRepositoryInterface $accountRepository
    ) {
    }

    /**
     * @return Collection<int, array{account_uuid: string, name: string}>
     */
    public function execute(int $userId): Collection
    {
        return collect($this->accountRepository->findSummariesByUserId($userId));
    }
}
