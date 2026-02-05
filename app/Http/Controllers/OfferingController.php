<?php

namespace App\Http\Controllers;

use App\Models\Offering;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class OfferingController extends Controller
{
  public function index(Request $request)
  {
    $user = Auth::user();
    $query = Offering::query();

    // Role Check for inactive
    if ($request->boolean('include_inactive') && in_array($user->role, ['admin', 'psadmin'])) {
      // No filter
    } else {
      $query->where('is_active', true);
    }

    // Conflict of Interest Filter for Associates
    if ($user->role === 'associate' && $user->category) {
      $query->where('category', '!=', $user->category);
    }

    $offerings = $query->latest()->paginate(50);

    return Inertia::render('Offerings/Index', [
      'offerings' => $offerings,
    ]);
  }

  public function store(Request $request)
  {
    // Only PSAdmin/Admin
    if (!in_array($request->user()->role, ['admin', 'psadmin'])) {
      abort(403);
    }

    $validated = $request->validate([
      'name' => 'required|string|max:255',
      'description' => 'nullable|string',
      'base_price' => 'nullable|numeric',
      'commission_rate' => 'nullable|numeric',
      'form_schema' => 'nullable|array',
      'commission_config' => 'nullable|array',
    ]);

    $offering = Offering::create([
      ...$validated,
      'owner_id' => $request->user()->id,
      'is_active' => true
    ]);

    return redirect()->back()->with('success', 'Offering created.');
  }

  public function create()
  {
    if (!in_array(Auth::user()->role, ['admin', 'psadmin'])) abort(403);
    return Inertia::render('Offerings/Create');
  }

  public function edit(Offering $offering)
  {
    if (!in_array(Auth::user()->role, ['admin', 'psadmin'])) abort(403);
    return Inertia::render('Offerings/Create', [
      'offering' => $offering
    ]);
  }

  public function update(Request $request, Offering $offering)
  {
    if (!in_array($request->user()->role, ['admin', 'psadmin'])) {
      abort(403);
    }

    $validated = $request->validate([
      'name' => 'sometimes|string',
      'is_active' => 'boolean',
      // ... other fields
    ]);

    $offering->update($validated);

    return back();
  }
}
