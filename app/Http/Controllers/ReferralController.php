<?php

namespace App\Http\Controllers;

use App\Models\Offering;
use App\Models\Referral;
use App\Services\ReferralService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class ReferralController extends Controller
{
  protected $referralService;

  public function __construct(ReferralService $referralService)
  {
    $this->referralService = $referralService;
  }

  public function index(Request $request)
  {
    $user = Auth::user();
    $query = Referral::with(['offering', 'user', 'commissions']);

    // Role based filtering
    if ($user->role === 'associate') {
      $query->where('user_id', $user->id);
    }

    // Filters
    if ($request->has('status')) {
      $query->where('status', $request->status);
    }

    $referrals = $query->latest()->paginate(20);

    return Inertia::render('Referrals/Index', [
      'referrals' => $referrals,
      'filters' => $request->only(['status']),
    ]);
  }

  public function store(Request $request)
  {
    $user = Auth::user();

    $validated = $request->validate([
      'offering_id' => 'required|uuid|exists:offerings,id',
      'client_name' => 'required|string|max:255',
      'client_contact' => 'nullable|string|max:255',
      'notes' => 'nullable|string',
      'metadata' => 'nullable|array',
    ]);

    // Conflict of Interest Check
    if ($user->role === 'associate') {
      $offering = Offering::find($validated['offering_id']);
      if ($offering && $user->category && $offering->category === $user->category) {
        return back()->withErrors(['offering_id' => 'Conflicto de intereses: No puedes referir servicios de tu misma categoría.']);
      }
    }

    $this->referralService->create($validated, $user);

    return redirect()->route('referrals.index')->with('success', 'Referido creado exitosamente.');
  }

  public function create(Request $request)
  {
    $offerings = Offering::where('is_active', true)->get();
    $selectedOffering = null;

    if ($request->has('offering_id')) {
      $selectedOffering = $offerings->where('id', $request->offering_id)->first();
    }

    return Inertia::render('Referrals/Create', [
      'offerings' => $offerings,
      'offering' => $selectedOffering,
    ]);
  }

  public function show(Referral $referral)
  {
    // Authorization check (User can only see own, Admin sees all)
    if (!in_array(Auth::user()->role, ['admin', 'psadmin']) && Auth::id() !== $referral->user_id) {
      abort(403);
    }

    return Inertia::render('Referrals/Show', [
      'referral' => $referral->load(['offering', 'commissions']),
    ]);
  }

  public function update(Request $request, Referral $referral)
  {
    // Permission Check (PSAdmin only for status)
    // Or if using Policy: $this->authorize('update', $referral);
    // Node logic: Only PSAdmin updates status.

    if (!in_array($request->user()->role, ['admin', 'psadmin']) && $request->has('status')) {
      abort(403, 'Unauthorized to update status');
    }

    $validated = $request->validate([
      'status' => 'sometimes|string',
      'deal_value' => 'nullable|numeric',
      'revenue_generated' => 'nullable|numeric',
      'notes' => 'nullable|string',
    ]);

    $this->referralService->updateStatus(
      $referral,
      $validated['status'] ?? $referral->status,
      $request->user(),
      $validated
    );

    return back()->with('success', 'Referido actualizado.');
  }
}
