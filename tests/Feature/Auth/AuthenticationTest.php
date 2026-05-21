<?php

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;
use Inertia\Testing\AssertableInertia as Assert;

it('ログイン画面を表示できる', function () {
    $this->get(route('login', absolute: false))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Auth/Login'));
});

it('POST /login で認証され /dashboard へリダイレクトされる', function () {
    $user = User::factory()->create();

    $this->post(route('login', absolute: false), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticatedAs($user);
});

it('パスワードが不正な場合はバリデーションエラーになる', function () {
    $user = User::factory()->create();

    $this->post(route('login', absolute: false), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ])->assertSessionHasErrors('email');
});

it('POST /logout でセッションが破棄される', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/logout')
        ->assertRedirect(route('top', absolute: false));

    $this->assertGuest();
});

it('ログイン済み状態で GET /logout にアクセスしてもログアウトされず 404 になる', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('logout', absolute: false))
        ->assertNotFound();

    $this->assertAuthenticatedAs($user);
});

it('未ログイン状態で GET /logout にアクセスすると 404 になる', function () {
    $this->get(route('logout', absolute: false))
        ->assertNotFound();

    $this->assertGuest();
});

it('未ログイン状態で /dashboard にアクセスすると /login へリダイレクトされる', function () {
    $this->get(route('dashboard', absolute: false))
        ->assertRedirect(route('login', absolute: false));
});

it('ログインに成功するとレートリミットのカウントがリセットされる', function () {
    $user = User::factory()->create();

    for ($i = 0; $i < 3; $i++) {
        $this->post(route('login', absolute: false), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);
    }

    $this->post(route('login', absolute: false), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('dashboard', absolute: false));

    $throttleKey = LoginRequest::create(route('login', absolute: false), 'POST', [
        'email' => $user->email,
    ])->throttleKey();

    expect(RateLimiter::attempts($throttleKey))->toBe(0);
});

it('ログイン失敗が5回を超えるとレートリミットエラーになる', function () {
    $user = User::factory()->create();

    for ($i = 0; $i < 5; $i++) {
        $this->post(route('login', absolute: false), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);
    }

    $this->post(route('login', absolute: false), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ])->assertSessionHasErrors('email');
});
