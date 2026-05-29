<?php

namespace Database\Factories;

use App\Infrastructure\Persistence\Eloquent\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Account>
 */
class AccountFactory extends Factory
{
    // Account は会計側の Eloquent Model として Infrastructure 配下に置くため、Factory 側でも対象を明示する。
    protected $model = Account::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_uuid' => (string) Str::uuid(),
            'user_id' => User::factory(),
            'name' => fake()->words(2, true),
        ];
    }
}
