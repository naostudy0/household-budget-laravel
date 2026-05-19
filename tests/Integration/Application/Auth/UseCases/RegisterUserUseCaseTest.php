<?php

use App\Application\Auth\DTOs\RegisterUserInput;
use App\Application\Auth\UseCases\RegisterUserUseCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('ユーザーが正しく登録されて ID が返却される', function () {
    $useCase = app(RegisterUserUseCase::class);

    $result = $useCase->execute(new RegisterUserInput(
        name: 'テストユーザー',
        email: 'test@example.com',
        password: 'password',
    ));

    expect($result->userId)->toBeInt()->toBeGreaterThan(0);
    $this->assertDatabaseHas('users', [
        'name' => 'テストユーザー',
        'email' => 'test@example.com',
    ]);

    $stored = User::find($result->userId);
    expect(Hash::check('password', $stored->password))->toBeTrue();
});
