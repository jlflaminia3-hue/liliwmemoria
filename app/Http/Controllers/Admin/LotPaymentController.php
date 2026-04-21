<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Lot;
use App\Models\LotPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class LotPaymentController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'status' => ['nullable', Rule::in(LotPayment::STATUSES)],
            'client_id' => 'nullable|exists:clients,id',
            'search' => 'nullable|string|max:255',
        ]);

        $status = $validated['status'] ?? 'all';
        $clientId = $validated['client_id'] ?? null;
        $search = trim((string) ($validated['search'] ?? ''));

        $query = LotPayment::with(['client', 'lot', 'reservation'])
            ->orderByDesc('created_at');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($clientId) {
            $query->where('client_id', $clientId);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('payment_number', 'like', '%'.$search.'%')
                    ->orWhere('reference_number', 'like', '%'.$search.'%')
                    ->orWhereHas('client', function ($cq) use ($search) {
                        $cq->where('first_name', 'like', '%'.$search.'%')
                            ->orWhere('last_name', 'like', '%'.$search.'%');
                    })
                    ->orWhereHas('lot', function ($lq) use ($search) {
                        $lq->where('lot_number', 'like', '%'.$search.'%')
                            ->orWhere('section', 'like', '%'.$search.'%');
                    });
            });
        }

        $payments = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => LotPayment::count(),
            'pending' => LotPayment::where('status', LotPayment::STATUS_PENDING)->count(),
            'paid' => LotPayment::where('status', LotPayment::STATUS_PAID)->count(),
            'verified' => LotPayment::where('status', LotPayment::STATUS_VERIFIED)->count(),
            'completed' => LotPayment::where('status', LotPayment::STATUS_COMPLETED)->count(),
            'overdue' => LotPayment::where('status', LotPayment::STATUS_PENDING)
                ->whereNotNull('due_date')
                ->where('due_date', '<', now()->toDateString())
                ->count(),
        ];

        $clients = Client::query()->orderBy('last_name')->orderBy('first_name')->get(['id', 'first_name', 'last_name']);
        $lots = Lot::query()->orderBy('section')->orderBy('lot_number')->get(['id', 'lot_number', 'section', 'status']);

        return view('admin.lot-payments.index', compact('payments', 'stats', 'clients', 'lots', 'status', 'clientId', 'search'));
    }

    public function create(Request $request)
    {
        $clientId = $request->input('client_id');
        $lotId = $request->input('lot_id');

        $clients = Client::query()->orderBy('last_name')->orderBy('first_name')->get(['id', 'first_name', 'last_name']);
        $lots = Lot::query()
            ->where('status', 'available')
            ->orWhere('id', $lotId)
            ->orderBy('section')
            ->orderBy('lot_number')
            ->get(['id', 'lot_number', 'section', 'status']);

        $selectedClient = $clientId ? Client::find($clientId) : null;
        $selectedLot = $lotId ? Lot::find($lotId) : null;

        return view('admin.lot-payments.create', compact('clients', 'lots', 'selectedClient', 'selectedLot'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'lot_id' => 'required|exists:lots,id',
            'reservation_id' => 'nullable|exists:reservations,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:today',
            'method' => 'nullable|string|max:50',
            'reference_number' => 'nullable|string|max:100',
            'status' => ['nullable', Rule::in(LotPayment::STATUSES)],
            'notes' => 'nullable|string',
        ]);

        $payment = LotPayment::create([
            'client_id' => $validated['client_id'],
            'lot_id' => $validated['lot_id'],
            'reservation_id' => $validated['reservation_id'] ?? null,
            'payment_number' => LotPayment::generatePaymentNumber(),
            'amount' => $validated['amount'],
            'payment_date' => $validated['payment_date'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'method' => $validated['method'] ?? null,
            'reference_number' => $validated['reference_number'] ?? null,
            'status' => $validated['status'] ?? LotPayment::STATUS_PENDING,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('admin.lot-payments.show', $payment)
            ->with('success', 'Regular lot payment created successfully.');
    }

    public function show(LotPayment $lotPayment)
    {
        $lotPayment->load(['client', 'lot', 'reservation', 'verifier']);

        return view('admin.lot-payments.show', compact('lotPayment'));
    }

    public function update(Request $request, LotPayment $lotPayment)
    {
        $validated = $request->validate([
            'amount' => 'nullable|numeric|min:0.01',
            'payment_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'method' => 'nullable|string|max:50',
            'reference_number' => 'nullable|string|max:100',
            'status' => ['nullable', Rule::in(LotPayment::STATUSES)],
            'notes' => 'nullable|string',
        ]);

        $lotPayment->update($validated);

        return back()->with('success', 'Payment updated successfully.');
    }

    public function destroy(LotPayment $lotPayment)
    {
        if ($lotPayment->receipt_path) {
            Storage::disk('public')->delete($lotPayment->receipt_path);
        }

        $lotPayment->delete();

        return redirect()
            ->route('admin.lot-payments.index')
            ->with('success', 'Payment deleted successfully.');
    }

    public function markPaid(Request $request, LotPayment $lotPayment)
    {
        $validated = $request->validate([
            'payment_date' => 'nullable|date',
            'method' => 'nullable|string|max:50',
            'reference_number' => 'nullable|string|max:100',
            'receipt' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($lotPayment, $validated, $request) {
            $updateData = [
                'status' => LotPayment::STATUS_PAID,
                'payment_date' => $validated['payment_date'] ?? now()->toDateString(),
                'method' => $validated['method'] ?? null,
                'reference_number' => $validated['reference_number'] ?? null,
                'notes' => $validated['notes'] ?? $lotPayment->notes,
            ];

            if ($request->hasFile('receipt')) {
                $path = Storage::disk('public')->putFile("lot-payment-receipts/{$lotPayment->id}", $request->file('receipt'));
                $updateData['receipt_path'] = $path;
            }

            $lotPayment->update($updateData);
        });

        return back()->with('success', 'Payment marked as paid.');
    }

    public function verify(Request $request, LotPayment $lotPayment)
    {
        abort_unless($lotPayment->status === LotPayment::STATUS_PAID, 400, 'Payment must be in Paid status to verify.');

        $lotPayment->markAsVerified(auth()->id());

        return back()->with('success', 'Payment verified successfully.');
    }

    public function complete(Request $request, LotPayment $lotPayment)
    {
        abort_unless($lotPayment->status === LotPayment::STATUS_VERIFIED, 400, 'Payment must be verified to complete.');

        DB::transaction(function () use ($lotPayment) {
            $lotPayment->markAsCompleted();

            $lotPayment->lot->update(['status' => 'sold', 'is_occupied' => true]);
        });

        return back()->with('success', 'Payment completed. Lot marked as sold.');
    }

    public function downloadReceipt(LotPayment $lotPayment)
    {
        abort_unless($lotPayment->receipt_path, 404, 'No receipt available.');

        $extension = pathinfo($lotPayment->receipt_path, PATHINFO_EXTENSION);

        return Storage::disk('public')->download($lotPayment->receipt_path, "LotPayment-{$lotPayment->payment_number}.{$extension}");
    }
}
