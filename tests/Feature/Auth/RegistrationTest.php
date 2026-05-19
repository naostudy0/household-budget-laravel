<?php

use Inertia\Testing\AssertableInertia as Assert;

it('ユーザー登録画面を表示できる', function () {
    $this->get(route('register', absolute: false))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Auth/Register'));
});

it('POST /register でユーザーが作成され /dashboard へリダイレクトされる', function () {
    $this->post(route('register', absolute: false), [
        'name' => 'テストユーザー',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertRedirect(route('dashboard', absolute: false));

    $this->assertDatabaseHas('users', [
        'name' => 'テストユーザー',
        'email' => 'test@example.com',
    ]);

    $this->assertAuthenticated();
});

it('登録に成功するとセッションIDが再生成される', function () {
    $sessionCookieName = config('session.cookie');
    $sessionId = str_repeat('a', 40);

    $response = $this
        ->withCookie($sessionCookieName, $sessionId)
        ->post(route('register', absolute: false), [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

    $response->assertRedirect(route('dashboard', absolute: false));

    expect($response->getCookie($sessionCookieName)->getValue())->not->toBe($sessionId);
});

it('メール形式が不正な場合はバリデーションエラーになる', function () {
    $this->post(route('register', absolute: false), [
        'name' => 'テストユーザー',
        'email' => 'not-an-email',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasErrors('email');
});

it('パスワードが短すぎる場合はバリデーションエラーになる', function () {
    $this->post(route('register', absolute: false), [
        'name' => 'テストユーザー',
        'email' => 'test@example.com',
        'password' => 'short',
        'password_confirmation' => 'short',
    ])->assertSessionHasErrors('password');
});
