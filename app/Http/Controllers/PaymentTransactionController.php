<?php

namespace App\Http\Controllers;

use App\Models\PaymentPlan;
use App\Models\PaymentTransaction;
use App\Services\Payments\PaymentAllocator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentTransactionController extends Controller
{
    public function store(Request $request, PaymentPlan $paymentPlan, PaymentAllocator $allocator)
    {
        $validated = $request->validate([
            'transaction_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|string|max:50',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'receipt' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png',
        ]);

        $transaction = PaymentTransaction::create([
            'payment_plan_id' => $paymentPlan->id,
            'client_id' => $paymentPlan->client_id,
            'created_by' => auth()->id(),
            'transaction_date' => $validated['transaction_date'],
            'amount' => $validated['amount'],
            'method' => $validated['method'],
            'reference_number' => $validated['reference_number'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        if ($request->hasFile('receipt')) {
            $path = Storage::disk('public')->putFile("payment-receipts/{$transaction->id}", $request->file('receipt'));
            $transaction->receipt_path = $path;
            $transaction->save();
        }

        $allocator->allocate($paymentPlan, $transaction);

        return redirect()
            ->route('admin.payments.show', $paymentPlan)
            ->with('success', 'Payment recorded.');
    }

    public function downloadReceipt(PaymentTransaction $paymentTransaction): StreamedResponse
    {
        abort_unless($paymentTransaction->receipt_path, 404);

        return Storage::disk('public')->download($paymentTransaction->receipt_path);
    }

    public function invoice(PaymentTransaction $paymentTransaction, Request $request)
    {
        $paymentTransaction->load(['plan.client', 'allocations.installment']);

        $view = view('admin.payments.invoice', [
            'transaction' => $paymentTransaction,
        ]);

        if ($request->boolean('download')) {
            $filename = "invoice-{$paymentTransaction->id}.html";

            return response()->streamDownload(function () use ($view) {
                echo $view->render();
            }, $filename, ['Content-Type' => 'text/html; charset=UTF-8']);
        }

        return $view;
    }
}
