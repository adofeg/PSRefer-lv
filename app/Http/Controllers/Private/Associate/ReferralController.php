<?php

namespace App\Http\Controllers\Private\Associate;

use App\Http\Requests\Associate\ReferralRequest;
use App\Mail\NewReferralNotification;
use App\Models\Offering;
use App\Models\Referral;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class ReferralController extends AssociateController
{
    public function index(ReferralRequest $request)
    {
        $user = Auth::user();
        $associate = $user->associate;

        if (! $associate) {
            return Inertia::render('Private/Associate/Referrals/Index', [
                'referrals' => [],
                'filters' => $request->only(['search']),
            ]);
        }

        $referrals = Referral::query()
            ->where('associate_id', $associate->id)
            ->with(['offering'])
            ->when($request->search, function ($query, $search) {
                $query->where('client_name', 'like', "%{$search}%")
                    ->orWhere('client_contact', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Private/Associate/Referrals/Index', [
            'referrals' => $referrals,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(ReferralRequest $request)
    {
        $offeringId = $request->validated('offering_id');
        $selectedOffering = null;
        $user = Auth::user();
        $associate = $user->associate;

        if ($offeringId) {
            $selectedOffering = Offering::find($offeringId);
            if ($selectedOffering && $associate && $selectedOffering->category_id === $associate->category_id) {
                // Conflict
                $selectedOffering = null;
            }
        }

        $offerings = Offering::query()
            ->where('is_active', true)
            ->excludeCategory($associate?->category)
            ->get(['id', 'name', 'base_commission']);

        return Inertia::render('Private/Associate/Referrals/Create', [
            'offerings' => $offerings,
            'selectedOffering' => $selectedOffering,
        ]);
    }

    public function store(ReferralRequest $request, \App\Actions\Referrals\CreateReferralAction $action)
    {
        $user = Auth::user();
        $associate = $user->associate;

        if (! $associate) {
            abort(403, 'Usuario no es un asociado válido.');
        }

        $referral = $action->execute($request->toStoreData($associate->id));

        // Note: NewReferralAlertMail is already sent by CreateReferralAction to admins.
        // Associate-specific notification (to offering contacts) can be handled here if needed,
        // but often the Action should handle all business-level notifications.
        // Current AssociateController sent NewReferralNotification to $offering->notification_emails.
        
        $offering = $referral->offering;
        $recipients = $offering->notification_emails ?? [];

        if (! empty($recipients)) {
            try {
                Mail::to($recipients)->send(new NewReferralNotification($referral));
            } catch (\Exception $e) {
                Log::error('Failed to send referral notification: '.$e->getMessage());
            }
        }

        return redirect()->route('associate.referrals.index')->with('success', 'Referido creado exitosamente.');
    }
}
