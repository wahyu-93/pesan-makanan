<?php

namespace App\Http\Controllers;

use App\Models\Barcode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DownloadQBarcodeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Barcode $barcode)
    {
        $path = $barcode->images;

        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'QR Code tidak ditemukan.');
        }

        return Storage::disk('public')->download($path);
    }
}
