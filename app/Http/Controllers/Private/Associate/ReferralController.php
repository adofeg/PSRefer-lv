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
        $associate = $user->associate; // Correct accessor from User.php

        $referrals = Referral::query()
            ->where('associate_id', $associate?->id) 
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
            ->get(['id', 'name', 'base_commission', 'commission_rate']);

        return Inertia::render('Private/Associate/Referrals/Create', [
            'offerings' => $offerings,
            'selectedOffering' => $selectedOffering,
        ]);
    }

    public function store(ReferralRequest $request)
    {
        $validated = $request->validated();

        $user = Auth::user();
        $associate = $user->associate;

        if (!$associate) {
            abort(403, 'Usuario no es un asociado válido.');
        }
        
        $offering = Offering::findOrFail($validated['offering_id']);
        if ($associate->category && $offering->category === $associate->category) {
             abort(403, 'Conflicto de intereses.');
        }

        $referral = Referral::create([
            'associate_id' => $associate->id,
            'offering_id' => $validated['offering_id'],
            'client_name' => $validated['client_name'],
            'client_contact' => $validated['client_email'] . ' | ' . $validated['client_phone'],
            'status' => 'Prospecto',
            'notes' => $validated['notes'],
            'metadata' => ['client_state' => $validated['client_state']], // Store State
        ]);

        // Email Notification Logic
        $recipients = $offering->notification_emails ?? [];

        if (!empty($recipients)) {
            try {
                Mail::to($recipients)->send(new NewReferralNotification($referral));
            } catch (\Exception $e) {
                Log::error('Failed to send referral notification: ' . $e->getMessage());
            }
        }

        return redirect()->route('associate.referrals.index')->with('success', 'Referido creado exitosamente.');
    }
}
