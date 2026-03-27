<?php
namespace App\Services;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class QrCodeService
{
    public function generate(string $ticketCode): string
    {
        $svg = QrCode::format('svg')->size(200)->generate($ticketCode);
        $path = 'qrcodes/' . $ticketCode . '.svg';
        Storage::disk('public')->put($path, $svg);
        return $path;
    }
}
