<?php

namespace App\Http\Controllers;

use App\Application\Accounts\DTOs\CreateAccountInput;
use App\Application\Accounts\Queries\AccountIndexQuery;
use App\Application\Accounts\UseCases\CreateAccountUseCase;
use App\Http\Requests\Accounts\AccountRequest;
use App\Infrastructure\Persistence\Eloquent\Models\Account;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class AccountController extends Controller
{
    public function index(AccountIndexQuery $query): Response
    {
        return Inertia::render('Accounts/Index', [
            'accounts' => $query->execute(Auth::user())->values(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Accounts/Create');
    }

    public function store(AccountRequest $request, CreateAccountUseCase $useCase): RedirectResponse
    {
        $useCase->execute(new CreateAccountInput(
            userId: Auth::id(),
            name: $request->validated('name'),
        ));

        return redirect(route('accounts.index'));
    }

    public function show(Account $account): Response
    {
        Gate::authorize('view', $account);

        return Inertia::render('Accounts/Show', [
            'account' => $this->serializeAccount($account),
        ]);
    }

    public function edit(Account $account): Response
    {
        Gate::authorize('update', $account);

        return Inertia::render('Accounts/Edit', [
            'account' => $this->serializeAccount($account),
        ]);
    }

    public function update(AccountRequest $request, Account $account): RedirectResponse
    {
        Gate::authorize('update', $account);

        $account->update([
            'name' => $request->validated('name'),
        ]);

        return redirect(route('accounts.show', $account));
    }

    public function destroy(Account $account): RedirectResponse
    {
        Gate::authorize('delete', $account);

        $account->delete();

        return redirect(route('accounts.index'));
    }

    /**
     * @return array{account_uuid: string, name: string}
     */
    private function serializeAccount(Account $account): array
    {
        return [
            'account_uuid' => $account->account_uuid,
            'name' => $account->name,
        ];
    }
}
