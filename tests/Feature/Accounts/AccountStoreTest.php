<?php

use App\Models\User;

it('会計単位の作成ではRequestのuser_idを信頼せずログインユーザーに紐づける', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $this->actingAs($user)
        ->post(route('accounts.store', absolute: false), [
            'name' => '生活費',
            'user_id' => $otherUser->getKey(),
        ])
        ->assertRedirect(route('accounts.index', absolute: false));

    $this->assertDatabaseHas('accounts', [
        'user_id' => $user->getKey(),
        'name' => '生活費',
    ]);
    $this->assertDatabaseMissing('accounts', [
        'user_id' => $otherUser->getKey(),
        'name' => '生活費',
    ]);
});
