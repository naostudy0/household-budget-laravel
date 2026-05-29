<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Accounts\Repositories\AccountRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\Account;

class EloquentAccountRepository implements AccountRepositoryInterface
{
    /**
     * @return array{account_id: int, account_uuid: string}
     */
    public function create(int $userId, string $name): array
    {
        $account = Account::create([
            'user_id' => $userId,
            'name' => $name,
        ]);

        return [
            'account_id' => $account->getKey(),
            'account_uuid' => $account->account_uuid,
        ];
    }
}
