<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

class GenerateQrcodeService {
    public function generateQr($barcodeId)
    {
        $qrCode = QrCode::margin(1)->size(200)->generate($_SERVER['HTTP_HOST'].'/'.$barcodeId);
        
        return $qrCode;
    }
}