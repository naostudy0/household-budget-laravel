<?php

namespace App\Application\Auth\UseCases;

use App\Application\Auth\DTOs\RegisterUserInput;
use App\Application\Auth\DTOs\RegisterUserResult;
use App\Domain\Auth\Repositories\UserRepositoryInterface;

readonly class RegisterUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {
    }

    public function execute(RegisterUserInput $input): RegisterUserResult
    {
        $userId = $this->userRepository->create(
            name: $input->name,
            email: $input->email,
            plainPassword: $input->password,
        );

        return new RegisterUserResult(userId: $userId);
    }
}
