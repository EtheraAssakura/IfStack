<?php

namespace App\Http\Controllers;

use App\Traits\GeneratesQrCodes;
use Illuminate\Http\Request;

class QrCodeController extends Controller
{
    use GeneratesQrCodes;

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'url' => 'required|url',
            'path' => 'required|string'
        ]);

        $url = $this->generateQrCode($validated['url'], $validated['path']);

        return response()->json([
            'url' => $url
        ]);
    }
}
