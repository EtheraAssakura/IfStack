<?php

namespace App\Traits;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Illuminate\Support\Facades\Storage;

trait GeneratesQrCodes
{
    protected function generateQrCode(string $url, string $path): string
    {
        $qrCode = new QrCode($url);
        $qrCode->setSize(300);
        $qrCode->setMargin(10);
        $qrCode->setErrorCorrectionLevel(new ErrorCorrectionLevelHigh());

        $writer = new SvgWriter();
        $result = $writer->write($qrCode);

        $fullPath = Storage::disk('public')->path($path);
        \Illuminate\Support\Facades\Log::info('Génération QR Code:', [
            'url' => $url,
            'path' => $path,
            'full_path' => $fullPath,
            'exists' => file_exists($fullPath)
        ]);

        Storage::disk('public')->put($path, $result->getString());
        $url = Storage::url($path);

        \Illuminate\Support\Facades\Log::info('QR Code généré:', [
            'url' => $url,
            'exists' => file_exists($fullPath)
        ]);

        return $url;
    }
}
