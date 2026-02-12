<?php

namespace App\Http\Controllers\Private\Associate;

use App\Http\Controllers\Controller;
use App\Models\Offering;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class OfferingController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $associate = $user->associate; // Accessor

        $offerings = Offering::query()
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%"); // Fix: search scope usage might be safer with custom query 
            })
            ->when($associate && $associate->category, function ($query) use ($associate) {
                $query->excludeCategory($associate->category);
            })
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Private/Associate/Offerings/Index', [
            'offerings' => $offerings,
            'filters' => $request->only(['search']),
        ]);
    }

    public function show(Offering $offering)
    {
        // Security check: Ensure the offering is not in the excluded category
        $user = Auth::user();
        $associateCategory = $user->category;
        
        if ($associateCategory && $offering->category === $associateCategory) {
             abort(403, 'No tienes permiso para ver esta oferta debido a un conflicto de intereses.');
        }

        return Inertia::render('Private/Associate/Offerings/Show', [
            'offering' => $offering->load('owner'),
        ]);
    }
}
