<?php

namespace App\Http\Controllers\Private\Settings;

use App\Actions\Categories\GetActiveCategoryNamesAction;
use App\Actions\Settings\DeleteUserAccountAction;
use App\Actions\Settings\UpdateUserSettingsAction;
use App\Http\Requests\Settings\ProfileRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends SettingsController
{
    public function edit(GetActiveCategoryNamesAction $categoriesAction): Response
    {
        return Inertia::render('Private/Settings/Profile/Index', [
            'user' => request()->user()->load('profileable'),
            'categories' => $categoriesAction->execute(),
        ]);
    }

    public function update(ProfileRequest $request, UpdateUserSettingsAction $action): RedirectResponse
    {
        $action->execute($request->user(), $request->toData());

        return back()->with('success', 'Configuración actualizada correctamente');
    }

    public function destroy(ProfileRequest $request, DeleteUserAccountAction $action): RedirectResponse
    {
        $request->validateForDelete();

        $action->execute($request->user(), $request->session());

        return redirect('/');
    }
}