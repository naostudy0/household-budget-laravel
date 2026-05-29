<?php

use App\Http\Requests\Accounts\AccountRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

function validAccountInput(array $overrides = []): array
{
    return array_merge([
        'name' => '生活費',
    ], $overrides);
}

it('有効な入力値はバリデーションを通過する', function () {
    $validator = Validator::make(validAccountInput(), (new AccountRequest)->rules());

    expect($validator->passes())->toBeTrue();
});

it('name が 100 文字ちょうどの場合はバリデーションを通過する', function () {
    $validator = Validator::make(
        validAccountInput(['name' => str_repeat('あ', 100)]),
        (new AccountRequest)->rules()
    );

    expect($validator->passes())->toBeTrue();
});

it('不正な入力値はバリデーションエラーになる', function (array $input, array $expectedErrorKeys) {
    $validator = Validator::make($input, (new AccountRequest)->rules());

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->keys())->toBe($expectedErrorKeys);

    foreach ($expectedErrorKeys as $key) {
        expect($validator->errors()->first($key))->not->toBe('');
    }
})->with([
    'name が空' => [
        'input' => validAccountInput(['name' => '']),
        'expectedErrorKeys' => ['name'],
    ],
    'name が未指定' => [
        'input' => Arr::except(validAccountInput(), 'name'),
        'expectedErrorKeys' => ['name'],
    ],
    'name が文字列ではない' => [
        'input' => validAccountInput(['name' => ['生活費']]),
        'expectedErrorKeys' => ['name'],
    ],
    'name が 100 文字を超える' => [
        'input' => validAccountInput(['name' => str_repeat('a', 101)]),
        'expectedErrorKeys' => ['name'],
    ],
]);
