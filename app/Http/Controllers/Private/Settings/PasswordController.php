<?php

namespace App\Http\Controllers\Private\Settings;

use App\Actions\Settings\ChangePasswordAction;
use App\Http\Requests\Settings\PasswordChangeRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PasswordController extends SettingsController
{
    public function edit(): Response
    {
        return Inertia::render('Settings/Password');
    }

    public function update(PasswordChangeRequest $request, ChangePasswordAction $action): RedirectResponse
    {
        $action->execute($request->user(), $request->toData());

        return back()->with('success', 'Contraseña actualizada correctamente');
    }
}