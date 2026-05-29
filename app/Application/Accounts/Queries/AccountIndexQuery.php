<?php

namespace App\Application\Accounts\Queries;

use App\Infrastructure\Persistence\Eloquent\Models\Account;
use App\Models\User;
use Illuminate\Support\Collection;

class AccountIndexQuery
{
    /**
     * @return Collection<int, array{account_uuid: string, name: string}>
     */
    public function execute(User $user): Collection
    {
        return Account::query()
            ->where('user_id', $user->getKey())
            ->orderBy('account_id')
            ->get(['account_uuid', 'name'])
            ->map(fn (Account $account): array => [
                'account_uuid' => $account->account_uuid,
                'name' => $account->name,
            ]);
    }
}
