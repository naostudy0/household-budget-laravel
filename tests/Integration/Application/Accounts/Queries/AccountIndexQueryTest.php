<?php

use App\Application\Accounts\Queries\AccountIndexQuery;
use App\Infrastructure\Persistence\Eloquent\Models\Account;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentAccountRepository;
use App\Models\User;

it('ログインユーザーに紐づく会計単位一覧を作成順で返す', function () {
    $user_id = User::factory()->create()->getKey();
    $otherUser = User::factory()->create();
    $firstAccount = Account::factory()->create([
        'user_id' => $user_id,
        'name' => '個人用',
    ]);
    $secondAccount = Account::factory()->create([
        'user_id' => $user_id,
        'name' => '生活費',
    ]);
    Account::factory()->create([
        'user_id' => $otherUser->getKey(),
        'name' => '他ユーザーの会計',
    ]);

    $accounts = (new AccountIndexQuery(new EloquentAccountRepository))
        ->execute($user_id);

    expect($accounts->all())->toBe([
        [
            'account_uuid' => $firstAccount->account_uuid,
            'name' => '個人用',
        ],
        [
            'account_uuid' => $secondAccount->account_uuid,
            'name' => '生活費',
        ],
    ]);
});
