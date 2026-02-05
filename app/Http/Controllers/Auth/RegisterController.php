<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\CreateUserAction;
use App\Data\Auth\UserData;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
  public function create()
  {
    return Inertia::render('Auth/Register');
  }

  public function store(Request $request, CreateUserAction $action)
  {
    // Citadel Law: Validate in Request or here if simple.
    // Using inline validation for speed as per user request to "migrate everything".
    // ideally should be RegisterRequest.

    $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:' . \App\Models\User::class,
      'password' => ['required', 'confirmed', Rules\Password::defaults()],
      'phone' => 'nullable|string|max:50',
      'category' => 'nullable|string',
      'offering_id' => 'nullable|uuid',
      'referred_by_id' => 'nullable|uuid',
    ]);

    $userData = new UserData(
      name: $request->name,
      email: $request->email,
      password: $request->password,
      phone: $request->phone,
      category: $request->category,
      referred_by_id: $request->referred_by_id,
      offering_id: $request->offering_id
    );

    $user = $action->execute($userData);

    Auth::login($user);

    return redirect(route('dashboard'));
  }
}
