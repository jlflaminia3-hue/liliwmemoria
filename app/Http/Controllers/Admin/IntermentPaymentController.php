<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deceased;
use App\Models\IntermentPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class IntermentPaymentController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'status' => ['nullable', Rule::in(['all', 'unpaid', 'partial', 'fully_paid'])],
            'search' => 'nullable|string|max:255',
        ]);

        $status = $validated['status'] ?? 'all';
        $search = trim((string) ($validated['search'] ?? ''));

        $query = Deceased::with(['client', 'lot', 'payments'])
            ->whereNotNull('burial_date')
            ->orderByDesc('burial_date');

        if ($status !== 'all') {
            $query->where('payment_status', $status);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('interment_number', 'like', '%' . $search . '%')
                    ->orWhereHas('client', function ($cq) use ($search) {
                        $cq->where('first_name', 'like', '%' . $search . '%')
                            ->orWhere('last_name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('lot', function ($lq) use ($search) {
                        $lq->where('lot_number', 'like', '%' . $search . '%')
                            ->orWhere('section', 'like', '%' . $search . '%');
                    });
            });
        }

        $interments = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => Deceased::whereNotNull('burial_date')->count(),
            'unpaid' => Deceased::whereNotNull('burial_date')->where('payment_status', 'unpaid')->count(),
            'partial' => Deceased::whereNotNull('burial_date')->where('payment_status', 'partial')->count(),
            'fully_paid' => Deceased::whereNotNull('burial_date')->where('payment_status', 'fully_paid')->count(),
            'total_unpaid_amount' => Deceased::whereNotNull('burial_date')
                ->where('payment_status', 'unpaid')
                ->sum(DB::raw('COALESCE(interment_fee, 15000)')),
            'total_partial_amount' => Deceased::whereNotNull('burial_date')
                ->where('payment_status', 'partial')
                ->sum(DB::raw('COALESCE(interment_fee, 15000) - (SELECT COALESCE(SUM(amount), 0) FROM interment_payments WHERE deceased_id = deceased.id))')),
        ];

        return view('admin.interment-payments.index', compact('interments', 'stats', 'status', 'search'));
    }

    public function show(Deceased $deceased)
    {
        $deceased->load(['lot', 'client', 'payments']);

        $totalFee = (float) ($deceased->interment_fee ?? Deceased::INTERMENT_FEE_TOTAL);

        return view('admin.interment-payments.show', [
            'deceased' => $deceased,
            'totalFee' => $totalFee,
            'totalPaid' => $deceased->total_paid,
            'remainingBalance' => $deceased->remaining_balance,
        ]);
    }

    public function storePayment(Request $request, Deceased $deceased)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'method' => 'required|string',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string',
            'receipt' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png',
        ]);

        DB::transaction(function () use ($validated, $deceased, $request) {
            $payment = IntermentPayment::create([
                'deceased_id' => $deceased->id,
                'amount' => $validated['amount'],
                'payment_date' => $validated['payment_date'],
                'method' => $validated['method'],
                'reference_number' => $validated['reference_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            if ($request->hasFile('receipt')) {
                $path = Storage::disk('public')->putFile("interment-receipts/{$payment->id}", $request->file('receipt'));
                $payment->receipt_path = $path;
                $payment->save();
            }
        });

        return redirect()
            ->route('admin.interment-payments.show', $deceased)
            ->with('success', 'Payment recorded successfully.');
    }

    public function paymentInvoice(Deceased $deceased, IntermentPayment $payment, Request $request)
    {
        abort_unless($payment->deceased_id === $deceased->id, 404);

        $view = view('admin.interment-payments.invoice', [
            'deceased' => $deceased,
            'payment' => $payment,
        ]);

        if ($request->boolean('download')) {
            $filename = "interment-invoice-{$payment->id}.html";

            return response()->streamDownload(function () use ($view) {
                echo $view->render();
            }, $filename, ['Content-Type' => 'text/html; charset=UTF-8']);
        }

        return $view;
    }

    public function paymentReceipt(Deceased $deceased, IntermentPayment $payment)
    {
        abort_unless($payment->deceased_id === $deceased->id, 404);
        abort_unless($payment->receipt_path, 404);

        $extension = pathinfo($payment->receipt_path, PATHINFO_EXTENSION);

        return Storage::disk('public')->download($payment->receipt_path, "Interment-Receipt-{$payment->id}.{$extension}");
    }
}
