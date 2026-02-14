<?php

namespace App\Actions\Auth;

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Validation\ValidationException;

class AuthenticateUserAction
{
    /**
     * Handle the authentication logic.
     *
     * @throws ValidationException
     */
    public function execute(LoginRequest $request): void
    {
        $request->authenticate();

        $request->session()->regenerate();
    }
}
