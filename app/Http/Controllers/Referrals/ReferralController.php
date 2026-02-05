<?php

namespace App\Http\Controllers\Referrals;

use App\Actions\Referrals\CreateReferralAction;
use App\Data\Referrals\ReferralData;
use App\Http\Controllers\Controller;
use App\Models\Offering;
use App\Models\Referral;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class ReferralController extends Controller
{
  public function index()
  {
    $referrals = Referral::where('user_id', Auth::id())
      ->with('offering:id,name')
      ->latest()
      ->paginate(10);

    return Inertia::render('Referrals/Index', [
      'referrals' => $referrals
    ]);
  }

  public function create(Request $request)
  {
    $offeringId = $request->query('offering_id');
    $offering = null;

    if ($offeringId) {
      $offering = Offering::findOrFail($offeringId);
    }

    // We need to fetch offerings to populate a select if no ID provided,
    // or just pass the specific offering data for the form schema.

    return Inertia::render('Referrals/Create', [
      'offering' => $offering,
      'offerings' => $offeringId ? [] : Offering::where('is_active', true)->select('id', 'name')->get()
    ]);
  }

  public function store(Request $request, CreateReferralAction $action)
  {
    $request->validate([
      'client_name' => 'required|string|max:255',
      'client_contact' => 'required|string|max:255',
      'offering_id' => 'required|uuid|exists:offerings,id',
      'metadata' => 'nullable|array',
      'notes' => 'nullable|string',
    ]);

    $referralData = new ReferralData(
      client_name: $request->client_name,
      client_contact: $request->client_contact,
      offering_id: $request->offering_id,
      metadata: $request->metadata,
      notes: $request->notes,
      user_id: Auth::id()
    );

    $action->execute($referralData);

    return redirect()->route('referrals.index')->with('success', 'Referral created successfully.');
  }
}
