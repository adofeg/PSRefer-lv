<?php

namespace App\Http\Controllers\Private\Settings;

use App\Http\Requests\Settings\AppearanceRequest;
use Inertia\Inertia;
use Inertia\Response;

class AppearanceController extends SettingsController
{
    public function __invoke(AppearanceRequest $request): Response
    {
        return Inertia::render('Private/Settings/Appearance/Index');
    }
}
