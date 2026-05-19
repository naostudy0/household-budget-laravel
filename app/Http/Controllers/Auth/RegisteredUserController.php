<?php

namespace App\Http\Controllers\Auth;

use App\Application\Auth\DTOs\RegisterUserInput;
use App\Application\Auth\UseCases\RegisterUserUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    public function store(RegisterRequest $request, RegisterUserUseCase $useCase): RedirectResponse
    {
        $validated = $request->validated();

        $result = $useCase->execute(new RegisterUserInput(
            name: $validated['name'],
            email: $validated['email'],
            password: $validated['password'],
        ));

        Auth::loginUsingId($result->userId);
        event(new Registered(Auth::user()));

        return redirect(route('dashboard'));
    }
}
