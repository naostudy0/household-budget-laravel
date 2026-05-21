<?php

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

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
    'email が文字列ではない' => [
        'input' => validLoginInput(['email' => ['user@example.com']]),
        'expectedErrorKeys' => ['email'],
    ],
    'email 形式が不正' => [
        'input' => validLoginInput(['email' => 'not-an-email']),
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
    'password が文字列ではない' => [
        'input' => validLoginInput(['password' => ['password']]),
        'expectedErrorKeys' => ['password'],
    ],
    'email と password が空' => [
        'input' => validLoginInput([
            'email' => '',
            'password' => '',
        ]),
        'expectedErrorKeys' => ['email', 'password'],
    ],
    'email と password が未指定' => [
        'input' => [],
        'expectedErrorKeys' => ['email', 'password'],
    ],
    'email と password が文字列ではない' => [
        'input' => validLoginInput([
            'email' => ['user@example.com'],
            'password' => ['password'],
        ]),
        'expectedErrorKeys' => ['email', 'password'],
    ],
]);
