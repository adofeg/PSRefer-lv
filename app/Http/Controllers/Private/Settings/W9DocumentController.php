<?php

namespace App\Http\Controllers\Private\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class W9DocumentController extends Controller
{
    /**
     * Handle the incoming request to view the W-9 document.
     */
    public function __invoke(Request $request): StreamedResponse
    {
        $user = $request->user();
        $path = $user->w9_file_url;

        if (!$path || !Storage::disk('local')->exists($path)) {
            abort(404, 'Documento no encontrado.');
        }

        // Security check: Only the owner or an admin can view the document
        if (!$user->hasRole(['admin', 'psadmin'])) {
            // For now, simple check since the URL is on the user model via profileable
            // In a more complex scenario, we'd check against the associate ID
        }

        $fileName = basename($path);
        
        return Storage::disk('local')->response($path, $fileName, [
            'Content-Disposition' => 'inline; filename="' . $fileName . '"'
        ]);
    }
}
