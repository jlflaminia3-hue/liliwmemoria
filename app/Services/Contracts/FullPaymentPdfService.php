<?php

namespace App\Services\Contracts;

use App\Models\LotPayment;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Storage;

class FullPaymentPdfService
{
    public function renderPdfBinary(LotPayment $payment): string
    {
        $payment->loadMissing(['client', 'lot', 'reservation']);

        $html = view('contracts.full-payment-pdf', [
            'payment' => $payment,
            'client' => $payment->client,
            'lot' => $payment->lot,
            'reservation' => $payment->reservation,
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

    public function storePdf(LotPayment $payment): string
    {
        $binary = $this->renderPdfBinary($payment);
        $path = 'contracts/full-payment-'.$payment->id.'.pdf';
        Storage::disk('local')->put($path, $binary);

        return $path;
    }

    public function getPdfBinary(LotPayment $payment): string
    {
        if ($payment->pdf_path && Storage::disk('local')->exists($payment->pdf_path)) {
            return Storage::disk('local')->get($payment->pdf_path);
        }

        return $this->renderPdfBinary($payment);
    }
}
