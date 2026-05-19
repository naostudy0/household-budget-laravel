<?php

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

function validLoginInput(array $overrides = []): array
{
    return array_merge([
        'email' => 'user@example.com',
        'password' => 'password',
    ], $overrides);
}

it('有効な入力値はバリデーションを通過する', function () {
    $validator = Validator::make(validLoginInput(), (new LoginRequest)->rules());

    expect($validator->passes())->toBeTrue();
});

it('不正な入力値はバリデーションエラーになる', function (array $input, array $expectedErrorKeys) {
    $validator = Validator::make($input, (new LoginRequest)->rules());

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->keys())->toBe($expectedErrorKeys);

    foreach ($expectedErrorKeys as $key) {
        expect($validator->errors()->first($key))->not->toBe('');
    }
})->with([
    'email が空' => [
        'input' => validLoginInput(['email' => '']),
        'expectedErrorKeys' => ['email'],
    ],
    'email が未指定' => [
        'input' => Arr::except(validLoginInput(), 'email'),
        'expectedErrorKeys' => ['email'],
    ],
    'password が空' => [
        'input' => validLoginInput(['password' => '']),
        'expectedErrorKeys' => ['password'],
    ],
    'password が未指定' => [
        'input' => Arr::except(validLoginInput(), 'password'),
        'expectedErrorKeys' => ['password'],
    ],
    'email 形式が不正' => [
        'input' => validLoginInput(['email' => 'not-an-email']),
        'expectedErrorKeys' => ['email'],
    ],
    'email と password が空' => [
        'input' => validLoginInput([
            'email' => '',
            'password' => '',
        ]),
        'expectedErrorKeys' => ['email', 'password'],
    ],
]);

it('認証に成功するとレートリミットがリセットされる', function () {
    $request = LoginRequest::create('/login', 'POST', validLoginInput());

    RateLimiter::shouldReceive('tooManyAttempts')
        ->once()
        ->with($request->throttleKey(), 5)
        ->andReturnFalse();

    Auth::shouldReceive('attempt')
        ->once()
        ->with(validLoginInput(), false)
        ->andReturnTrue();

    RateLimiter::shouldReceive('clear')
        ->once()
        ->with($request->throttleKey());

    $request->authenticate();
});

it('存在しないメールアドレスでは認証に失敗する', function () {
    $request = LoginRequest::create('/login', 'POST', [
        'email' => 'missing@example.com',
        'password' => 'password',
    ]);

    RateLimiter::shouldReceive('tooManyAttempts')
        ->once()
        ->with($request->throttleKey(), 5)
        ->andReturnFalse();
    RateLimiter::shouldReceive('hit')
        ->once()
        ->with($request->throttleKey());

    Auth::shouldReceive('attempt')
        ->once()
        ->with([
            'email' => 'missing@example.com',
            'password' => 'password',
        ], false)
        ->andReturnFalse();

    try {
        $request->authenticate();
        $this->fail('ValidationException was not thrown.');
    } catch (ValidationException $exception) {
        expect($exception->errors())->toHaveKey('email')
            ->and($exception->errors()['email'][0])->toBe(trans('auth.failed'));
    }
});
