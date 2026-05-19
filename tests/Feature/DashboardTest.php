<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

it('ログイン済みユーザーはダッシュボードにアクセスできる', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Dashboard'));
});
