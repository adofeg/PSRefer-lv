<?php

namespace App\Http\Controllers\Private\Shared;

use App\Http\Controllers\Controller;
use App\Models\FileAsset;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileDownloadController extends Controller
{
    /**
     * Download a file asset.
     */
    public function __invoke(FileAsset $fileAsset): StreamedResponse
    {
        // 1. Authorization: The user must be able to view the attachable model (the Referral)
        if (!Gate::allows('view', $fileAsset->attachable)) {
            abort(403, 'Unauthorized access to this file.');
        }

        // 2. Check if file exists on disk
        if (!Storage::disk($fileAsset->disk)->exists($fileAsset->path)) {
            abort(404, 'File not found on disk.');
        }

        // 3. Return file as a download
        return Storage::disk($fileAsset->disk)->download($fileAsset->path, $fileAsset->original_name);
    }
}
