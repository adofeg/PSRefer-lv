<?php

namespace App\Http\Controllers\Private\Admin;

use App\Actions\Settings\GetSmtpSettingsAction;
use App\Actions\Settings\TestSmtpConnectionAction;
use App\Actions\Settings\UpdateSmtpSettingsAction;
use App\Enums\RoleName;
use App\Http\Requests\Admin\SmtpSettingsRequest;
use Inertia\Inertia;

class SmtpSettingsController extends AdminController
{
    public function smtp(GetSmtpSettingsAction $action)
    {
        $this->authorizeAdminOnly();

        return Inertia::render('Private/Admin/Settings/SMTP', [
            'config' => $action->execute()
        ]);
    }

    public function updateSmtp(SmtpSettingsRequest $request, UpdateSmtpSettingsAction $action)
    {
        $this->authorizeAdminOnly();

        $action->execute($request->toData());

        return back()->with('success', 'SMTP Configuration updated successfully.');
    }

    public function testSmtp(GetSmtpSettingsAction $getAction, TestSmtpConnectionAction $testAction)
    {
        $this->authorizeAdminOnly();

        $result = $testAction->execute($getAction->execute());

        if ($result['success']) {
            return response()->json($result);
        }

        return response()->json($result, 400);
    }

    protected function authorizeAdminOnly(): void
    {
        $user = request()->user();
        if (!$user || !$user->hasRole(RoleName::Admin->value)) {
            abort(403, 'Solo administradores pueden gestionar SMTP.');
        }
    }
}