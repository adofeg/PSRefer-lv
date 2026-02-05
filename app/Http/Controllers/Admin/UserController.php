<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
  public function index()
  {
    return Inertia::render('Admin/Users/Index', [
      'users' => User::latest()->paginate(15)
    ]);
  }

  public function dashboard()
  {
    return Inertia::render('Admin/Dashboard', [
      'stats' => [
        'total_users' => User::count(),
        'total_offerings' => \App\Models\Offering::count(),
        'total_referrals' => \App\Models\Referral::count(),
        'pending_commissions' => \App\Models\Commission::where('status', 'pending')->count(),
      ]
    ]);
  }
}
