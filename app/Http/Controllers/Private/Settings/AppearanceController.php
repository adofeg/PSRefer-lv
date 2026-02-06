<?php

namespace App\Http\Controllers\Private\Settings;

use Inertia\Inertia;
use Inertia\Response;

class AppearanceController extends SettingsController
{
    public function __invoke(): Response
    {
        return Inertia::render('Private/Settings/Appearance/Index');
    }
}