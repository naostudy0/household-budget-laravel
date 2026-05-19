<?php

use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

function validRegisterInput(array $overrides = []): array
{
    return array_merge([
        'name' => 'テストユーザー',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ], $overrides);
}

it('有効な入力値はバリデーションを通過する', function () {
    $validator = Validator::make(validRegisterInput(), (new RegisterRequest)->rules());

    expect($validator->passes())->toBeTrue();
});

it('不正な入力値はバリデーションエラーになる', function (array $input, array $expectedErrorKeys) {
    $validator = Validator::make($input, (new RegisterRequest)->rules());

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->keys())->toBe($expectedErrorKeys);

    foreach ($expectedErrorKeys as $key) {
        expect($validator->errors()->first($key))->not->toBe('');
    }
})->with([
    'name が空' => [
        'input' => validRegisterInput(['name' => '']),
        'expectedErrorKeys' => ['name'],
    ],
    'name が未指定' => [
        'input' => Arr::except(validRegisterInput(), 'name'),
        'expectedErrorKeys' => ['name'],
    ],
    'name が 255 文字を超える' => [
        'input' => validRegisterInput(['name' => str_repeat('a', 256)]),
        'expectedErrorKeys' => ['name'],
    ],
    'email が空' => [
        'input' => validRegisterInput(['email' => '']),
        'expectedErrorKeys' => ['email'],
    ],
    'email が未指定' => [
        'input' => Arr::except(validRegisterInput(), 'email'),
        'expectedErrorKeys' => ['email'],
    ],
    'email 形式が不正' => [
        'input' => validRegisterInput(['email' => 'not-an-email']),
        'expectedErrorKeys' => ['email'],
    ],
    'password が空' => [
        'input' => validRegisterInput([
            'password' => '',
            'password_confirmation' => '',
        ]),
        'expectedErrorKeys' => ['password'],
    ],
    'password が未指定' => [
        'input' => Arr::except(validRegisterInput(), 'password'),
        'expectedErrorKeys' => ['password'],
    ],
    'password が短すぎる' => [
        'input' => validRegisterInput([
            'password' => 'short',
            'password_confirmation' => 'short',
        ]),
        'expectedErrorKeys' => ['password'],
    ],
    'password confirmation が一致しない' => [
        'input' => validRegisterInput(['password_confirmation' => 'different-password']),
        'expectedErrorKeys' => ['password'],
    ],
    'name email password が空' => [
        'input' => validRegisterInput([
            'name' => '',
            'email' => '',
            'password' => '',
            'password_confirmation' => '',
        ]),
        'expectedErrorKeys' => ['name', 'email', 'password'],
    ],
]);

it('既に存在するメールアドレスはバリデーションエラーになる', function () {
    User::factory()->create(['email' => 'test@example.com']);

    $validator = Validator::make(validRegisterInput(), (new RegisterRequest)->rules());

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->keys())->toBe(['email'])
        ->and($validator->errors()->first('email'))->not->toBe('');
});
