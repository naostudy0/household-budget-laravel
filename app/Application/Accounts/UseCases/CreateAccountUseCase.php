<?php

namespace App\Application\Accounts\UseCases;

use App\Application\Accounts\DTOs\CreateAccountInput;
use App\Application\Accounts\DTOs\CreateAccountResult;
use App\Domain\Accounts\Repositories\AccountRepositoryInterface;

readonly class CreateAccountUseCase
{
    public function __construct(
        private AccountRepositoryInterface $accountRepository,
    ) {
    }

    public function execute(CreateAccountInput $input): CreateAccountResult
    {
        $account = $this->accountRepository->create(
            userId: $input->userId,
            name: $input->name,
        );

        return new CreateAccountResult(
            accountId: $account['account_id'],
            accountUuid: $account['account_uuid'],
        );
    }
}
