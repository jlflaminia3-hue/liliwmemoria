<?php

namespace App\Services\Contracts;

use App\Models\ClientContract;
use Dompdf\Dompdf;
use Dompdf\Options;

class ContractPdfService
{
    public function renderPdfBinary(ClientContract $contract): string
    {
        $contract->loadMissing(['client', 'lot']);

        $html = view('contracts.pdf', [
            'contract' => $contract,
            'client' => $contract->client,
            'lot' => $contract->lot,
        ])->render();

        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->render();

        return $dompdf->output();
    }

    public function storePdf(ClientContract $contract): string
    {
        $binary = $this->renderPdfBinary($contract);
        $path = 'contracts/contract-' . $contract->id . '.pdf';
        \Illuminate\Support\Facades\Storage::disk('local')->put($path, $binary);
        return $path;
    }
}
