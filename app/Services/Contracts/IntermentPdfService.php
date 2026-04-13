<?php

namespace App\Services\Contracts;

use App\Models\Deceased;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Storage;

class IntermentPdfService
{
    public function renderPdfBinary(Deceased $interment): string
    {
        $interment->loadMissing(['client', 'lot']);

        $html = view('contracts.interment-pdf', [
            'interment' => $interment,
            'client' => $interment->client,
            'lot' => $interment->lot,
        ])->render();

        $options = new Options;
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->render();

        return $dompdf->output();
    }

    public function storePdf(Deceased $interment): string
    {
        $binary = $this->renderPdfBinary($interment);
        $path = 'interments/contracts/interment-contract-'.$interment->id.'.pdf';
        Storage::disk('local')->put($path, $binary);

        return $path;
    }
}
