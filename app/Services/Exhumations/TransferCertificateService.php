<?php

namespace App\Services\Exhumations;

use App\Models\Exhumation;
use Dompdf\Dompdf;
use Dompdf\Options;

class TransferCertificateService
{
    public function renderPdfBinary(Exhumation $exhumation): string
    {
        $exhumation->loadMissing(['deceased.lot', 'deceased.client']);

        $html = view('admin.exhumations.transfer-certificate-pdf', [
            'exhumation' => $exhumation,
            'deceased' => $exhumation->deceased,
            'lot' => $exhumation->deceased?->lot,
            'client' => $exhumation->deceased?->client,
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
}
