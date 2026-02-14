<?php

namespace App\Http\Controllers\Private\Admin;

use App\Actions\Settings\GetSmtpSettingsAction;
use App\Actions\Settings\TestSmtpConnectionAction;
use App\Actions\Settings\UpdateSmtpSettingsAction;
use App\Enums\RoleName;
use App\Http\Requests\Admin\SmtpSettingsRequest;
use App\Models\User;
use Inertia\Inertia;

class SmtpSettingsController extends AdminController
{
    public function smtp(SmtpSettingsRequest $request, GetSmtpSettingsAction $action)
    {
        $this->authorizeAdminOnly($request->user());

        return Inertia::render('Private/Admin/Settings/SMTP', [
            'config' => $action->execute()
        ]);
    }

    public function updateSmtp(SmtpSettingsRequest $request, UpdateSmtpSettingsAction $action)
    {
        $this->authorizeAdminOnly($request->user());

        $action->execute($request->toData());

        return back()->with('success', 'SMTP Configuration updated successfully.');
    }

    public function testSmtp(SmtpSettingsRequest $request, GetSmtpSettingsAction $getAction, TestSmtpConnectionAction $testAction)
    {
        $this->authorizeAdminOnly($request->user());

        $result = $testAction->execute($getAction->execute());

        if ($result['success']) {
            return response()->json($result);
        }

        return response()->json($result, 400);
    }

    protected function authorizeAdminOnly(?User $user): void
    {
        if (!$user || !$user->hasRole(RoleName::Admin->value)) {
            abort(403, 'Solo administradores pueden gestionar SMTP.');
        }
    }
}
