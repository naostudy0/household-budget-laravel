<?php

use App\Application\Accounts\DTOs\CreateAccountInput;
use App\Application\Accounts\UseCases\CreateAccountUseCase;
use App\Infrastructure\Persistence\Eloquent\Models\Account;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentAccountRepository;
use App\Models\User;

it('ログインユーザーに紐づく会計単位を作成する', function () {
    $user = User::factory()->create();

    $result = (new CreateAccountUseCase(new EloquentAccountRepository))
        ->execute(new CreateAccountInput(
            userId: $user->getKey(),
            name: '生活費',
        ));

    expect($result->accountId)->not->toBeNull()
        ->and($result->accountUuid)->not->toBeEmpty();

    $this->assertDatabaseHas('accounts', [
        'account_id' => $result->accountId,
        'account_uuid' => $result->accountUuid,
        'user_id' => $user->getKey(),
        'name' => '生活費',
    ]);
});

it('作成時に公開識別子を発行する', function () {
    $user = User::factory()->create();

    $result = (new CreateAccountUseCase(new EloquentAccountRepository))
        ->execute(new CreateAccountInput(
            userId: $user->getKey(),
            name: '個人用',
        ));

    $account = Account::findOrFail($result->accountId);

    expect($account->account_uuid)->toBe($result->accountUuid)
        ->and($account->getRouteKeyName())->toBe('account_uuid');
});
