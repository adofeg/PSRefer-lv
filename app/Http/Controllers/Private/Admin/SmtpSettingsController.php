<?php

namespace App\Http\Controllers\Private\Admin;

use App\Actions\Settings\GetSmtpSettingsAction;
use App\Actions\Settings\TestSmtpConnectionAction;
use App\Actions\Settings\UpdateSmtpSettingsAction;
use App\Http\Requests\Admin\SmtpSettingsRequest;
use Inertia\Inertia;

class SmtpSettingsController extends AdminController
{
    public function smtp(GetSmtpSettingsAction $action)
    {
        return Inertia::render('Admin/Settings/SMTP', [
            'config' => $action->execute()
        ]);
    }

    public function updateSmtp(SmtpSettingsRequest $request, UpdateSmtpSettingsAction $action)
    {
        $action->execute($request->toData());

        return back()->with('success', 'SMTP Configuration updated successfully.');
    }

    public function testSmtp(GetSmtpSettingsAction $getAction, TestSmtpConnectionAction $testAction)
    {
        $result = $testAction->execute($getAction->execute());

        if ($result['success']) {
            return response()->json($result);
        }

        return response()->json($result, 400);
    }
}