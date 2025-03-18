<?php

namespace App\Traits;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Support\Facades\Storage;

trait GeneratesQrCodes
{
    protected function generateQrCode(string $url, string $path): string
    {
        $result = (new Builder(
            writer: new SvgWriter(),
            writerOptions: [
                SvgWriter::WRITER_OPTION_EXCLUDE_XML_DECLARATION => true
            ],
            validateResult: false,
            data: $url,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 300,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin
        ))->build();

        // Change l'extension du fichier en .svg
        $path = str_replace('.png', '.svg', $path);
        Storage::disk('public')->put($path, $result->getString());

        return Storage::url($path);
    }
}
