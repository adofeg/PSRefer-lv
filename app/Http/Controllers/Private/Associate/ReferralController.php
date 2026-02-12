<?php

namespace App\Http\Controllers\Private\Associate;

use App\Actions\Categories\GetActiveCategoriesAction;
use App\Actions\Referrals\CreateReferralAction;
use App\Actions\Referrals\UpdateReferralAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReferralRequest; // Reusing Admin request for validation? 
// Admin Request might have rules that are too strict or irrelevant? 
// Let's check ReferralRequest content later. For now, assuming basic validation.
use App\Models\Offering;
use App\Models\Referral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ReferralController extends Controller
{
    public function index(Request $request)
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

    public function create(Request $request)
    {
        $offeringId = $request->query('offering_id');
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

    public function store(Request $request) 
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_email' => 'required|email|max:255',
            'client_phone' => 'required|string|max:20',
            'offering_id' => 'required|exists:offerings,id',
            'notes' => 'nullable|string',
        ]);

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
        ]);

        return redirect()->route('associate.referrals.index')->with('success', 'Referido creado exitosamente.');
    }
}
