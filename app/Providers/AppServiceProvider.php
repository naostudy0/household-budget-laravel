<?php

namespace App\Providers;

use App\Domain\Accounts\Repositories\AccountRepositoryInterface;
use App\Domain\Auth\Repositories\UserRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\Account;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentAccountRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentUserRepository;
use App\Policies\AccountPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AccountRepositoryInterface::class, EloquentAccountRepository::class);
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // app/Models 外にあるため、Policy の自動検出に頼らず明示登録する。
        Gate::policy(Account::class, AccountPolicy::class);
    }
}
