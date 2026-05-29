<?php

namespace App\Policies;

use App\Infrastructure\Persistence\Eloquent\Models\Account;
use App\Models\User;

class AccountPolicy
{
    public function view(User $user, Account $account): bool
    {
        return $this->owns($user, $account);
    }

    public function update(User $user, Account $account): bool
    {
        return $this->owns($user, $account);
    }

    public function delete(User $user, Account $account): bool
    {
        return $this->owns($user, $account);
    }

    private function owns(User $user, Account $account): bool
    {
        return (int) $user->getKey() === (int) $account->user_id;
    }
}
