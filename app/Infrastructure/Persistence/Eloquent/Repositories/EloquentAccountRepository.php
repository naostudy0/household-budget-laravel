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

    /**
     * @return array<int, array{account_uuid: string, name: string}>
     */
    public function findSummariesByUserId(int $userId): array
    {
        return Account::query()
            ->where('user_id', $userId)
            ->orderBy('account_id')
            ->get(['account_uuid', 'name'])
            ->map(fn (Account $account): array => [
                'account_uuid' => $account->account_uuid,
                'name' => $account->name,
            ])
            ->all();
    }
}
