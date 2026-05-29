<?php

use App\Infrastructure\Persistence\Eloquent\Models\Account;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

it('自分の会計単位だけ一覧に表示される', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $ownAccount = Account::factory()->create([
        'user_id' => $user->getKey(),
        'name' => '個人用',
    ]);
    Account::factory()->create([
        'user_id' => $otherUser->getKey(),
        'name' => '他ユーザーの会計',
    ]);

    $this->actingAs($user)
        ->get(route('accounts.index', absolute: false))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Accounts/Index')
            ->has('accounts', 1)
            ->where('accounts.0.account_uuid', $ownAccount->account_uuid)
            ->where('accounts.0.name', '個人用'))
        ->assertDontSee('他ユーザーの会計');
});

it('会計単位を作成できる', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('accounts.store', absolute: false), [
            'name' => '生活費',
        ])
        ->assertRedirect(route('accounts.index', absolute: false));

    $this->assertDatabaseHas('accounts', [
        'user_id' => $user->getKey(),
        'name' => '生活費',
    ]);
});

it('他ユーザーの会計単位詳細は表示できない', function () {
    $user = User::factory()->create();
    $otherAccount = Account::factory()->create([
        'user_id' => User::factory()->create()->getKey(),
    ]);

    $this->actingAs($user)
        ->get(route('accounts.show', $otherAccount, false))
        ->assertForbidden();
});

it('他ユーザーの会計単位編集画面は表示できない', function () {
    $user = User::factory()->create();
    $otherAccount = Account::factory()->create([
        'user_id' => User::factory()->create()->getKey(),
    ]);

    $this->actingAs($user)
        ->get(route('accounts.edit', $otherAccount, false))
        ->assertForbidden();
});

it('所有者は自分の会計単位詳細を表示できる', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create([
        'user_id' => $user->getKey(),
        'name' => '個人用',
    ]);

    $this->actingAs($user)
        ->get(route('accounts.show', $account, false))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Accounts/Show')
            ->where('account.account_uuid', $account->account_uuid)
            ->where('account.name', '個人用'));
});

it('所有者は自分の会計単位編集画面を表示できる', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create([
        'user_id' => $user->getKey(),
        'name' => '生活費',
    ]);

    $this->actingAs($user)
        ->get(route('accounts.edit', $account, false))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Accounts/Edit')
            ->where('account.account_uuid', $account->account_uuid)
            ->where('account.name', '生活費'));
});

it('所有者は自分の会計単位を更新できる', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create([
        'user_id' => $user->getKey(),
        'name' => '更新前',
    ]);

    $this->actingAs($user)
        ->put(route('accounts.update', $account, false), [
            'name' => '更新後',
        ])
        ->assertRedirect(route('accounts.show', $account, false));

    $this->assertDatabaseHas('accounts', [
        'account_id' => $account->account_id,
        'name' => '更新後',
    ]);
});

it('所有者は自分の会計単位を削除できる', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create([
        'user_id' => $user->getKey(),
    ]);

    $this->actingAs($user)
        ->delete(route('accounts.destroy', $account, false))
        ->assertRedirect(route('accounts.index', absolute: false));

    $this->assertDatabaseMissing('accounts', [
        'account_id' => $account->account_id,
    ]);
});

it('他ユーザーの会計単位は更新できない', function () {
    $user = User::factory()->create();
    $otherAccount = Account::factory()->create([
        'user_id' => User::factory()->create()->getKey(),
        'name' => '更新前',
    ]);

    $this->actingAs($user)
        ->put(route('accounts.update', $otherAccount, false), [
            'name' => '更新後',
        ])
        ->assertForbidden();

    $this->assertDatabaseHas('accounts', [
        'account_id' => $otherAccount->account_id,
        'name' => '更新前',
    ]);
});

it('他ユーザーの会計単位は削除できない', function () {
    $user = User::factory()->create();
    $otherAccount = Account::factory()->create([
        'user_id' => User::factory()->create()->getKey(),
    ]);

    $this->actingAs($user)
        ->delete(route('accounts.destroy', $otherAccount, false))
        ->assertForbidden();

    $this->assertDatabaseHas('accounts', [
        'account_id' => $otherAccount->account_id,
    ]);
});
