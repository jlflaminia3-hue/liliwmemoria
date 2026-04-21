<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\LotPayment;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AllPaymentsController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'type' => ['nullable', Rule::in(['all', 'reservation', 'lot-payment', 'installment', 'cash'])],
            'status' => ['nullable', Rule::in(['all', 'pending', 'paid', 'completed', 'overdue', 'partial', 'cash', 'installment'])],
            'client_id' => 'nullable|exists:clients,id',
            'search' => 'nullable|string|max:255',
        ]);

        $type = $validated['type'] ?? 'all';
        $status = $validated['status'] ?? 'all';
        $clientId = $validated['client_id'] ?? null;
        $search = trim((string) ($validated['search'] ?? ''));

        $query = Reservation::query()
            ->with(['client', 'lot', 'lotPayments', 'contract'])
            ->whereNotNull('payment_status')
            ->whereHas('lot');

        if ($clientId) {
            $query->where('client_id', $clientId);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->whereHas('client', function ($cq) use ($search) {
                    $cq->where('first_name', 'like', '%'.$search.'%')
                        ->orWhere('last_name', 'like', '%'.$search.'%');
                })
                    ->orWhereHas('lot', function ($lq) use ($search) {
                        $lq->where('lot_number', 'like', '%'.$search.'%')
                            ->orWhere('section', 'like', '%'.$search.'%');
                    });
            });
        }

        $reservations = $query->orderByDesc('created_at')->get();

        $reservationPayments = $reservations->map(function (Reservation $r) {
            $lotPayment = $r->lotPayments()->latest('created_at')->first();
            $contract = $r->contract;
            $totalAmount = (float) ($contract?->total_amount ?? 0);
            $amountPaid = (float) ($contract?->amount_paid ?? $r->amount_paid ?? 0);

            return [
                'id' => 'res-'.$r->id,
                'type' => 'reservation',
                'type_label' => 'Reservation',
                'payment_number' => $contract?->contract_number ?? 'RES-'.str_pad($r->id, 5, '0', STR_PAD_LEFT),
                'client' => $r->client,
                'lot' => $r->lot,
                'reservation' => $r,
                'amount' => $totalAmount,
                'amount_paid' => $amountPaid,
                'amount_due' => $totalAmount > 0 ? $totalAmount - $amountPaid : 0,
                'due_date' => $contract?->due_date ?? $r->expires_at,
                'payment_date' => $lotPayment?->payment_date ?? $r->reserved_at,
                'status' => $r->payment_status,
                'status_label' => match ($r->payment_status) {
                    'cash' => 'Cash',
                    'installment' => 'Installment',
                    default => 'Unknown',
                },
                'payment_method' => $lotPayment?->method,
                'created_at' => $r->created_at,
            ];
        });

        $lotPaymentQuery = LotPayment::query()
            ->with(['client', 'lot', 'reservation']);

        if ($clientId) {
            $lotPaymentQuery->where('client_id', $clientId);
        }

        if ($search !== '') {
            $lotPaymentQuery->where(function ($q) use ($search) {
                $q->where('payment_number', 'like', '%'.$search.'%')
                    ->orWhere('reference_number', 'like', '%'.$search.'%')
                    ->orWhereHas('client', function ($cq) use ($search) {
                        $cq->where('first_name', 'like', '%'.$search.'%')
                            ->orWhere('last_name', 'like', '%'.$search.'%');
                    })
                    ->orWhereHas('lot', function ($lq) use ($search) {
                        $lq->where('lot_number', 'like', '%'.$search.'%');
                    });
            });
        }

        $regularPayments = $lotPaymentQuery->orderByDesc('created_at')->get()->map(function (LotPayment $p) {
            $amount = (float) $p->amount;
            $isPaid = in_array($p->status, ['completed', 'verified']);

            return [
                'id' => 'lp-'.$p->id,
                'type' => 'lot-payment',
                'type_label' => 'Lot Payment',
                'payment_number' => $p->payment_number,
                'client' => $p->client,
                'lot' => $p->lot,
                'reservation' => $p->reservation,
                'amount' => $amount,
                'amount_paid' => $isPaid ? $amount : 0,
                'amount_due' => $amount,
                'due_date' => $p->due_date,
                'payment_date' => $p->payment_date,
                'status' => $p->status,
                'status_label' => $p->status_label,
                'payment_method' => $p->method,
                'created_at' => $p->created_at,
            ];
        });

        $allPayments = $reservationPayments->concat($regularPayments);

        if ($type !== 'all') {
            if ($type === 'cash') {
                $allPayments = $allPayments->where('status', 'cash');
            } elseif ($type === 'installment') {
                $allPayments = $allPayments->where('status', 'installment');
            } else {
                $typeMap = [
                    'reservation' => 'reservation',
                    'lot-payment' => 'lot-payment',
                ];
                $filterType = $typeMap[$type] ?? $type;
                $allPayments = $allPayments->where('type', $filterType);
            }
        }

        if ($status !== 'all') {
            if (in_array($status, ['cash', 'installment'])) {
                $allPayments = $allPayments->where('status', $status);
            }
        }

        $allPayments = $allPayments->sortByDesc('created_at')->values();

        $stats = [
            'total' => $allPayments->count(),
            'cash_count' => $reservationPayments->filter(fn ($p) => $p['status'] === 'cash')->count(),
            'installment_count' => $reservationPayments->filter(fn ($p) => $p['status'] === 'installment')->count() + $regularPayments->count(),
            'total_amount' => $allPayments->sum('amount'),
            'total_paid' => $allPayments->sum('amount_paid'),
            'total_due' => $allPayments->where('status', '!=', 'completed')->where('status', '!=', 'cash')->sum('amount_due'),
            'pending_count' => $allPayments->filter(fn ($p) => $p['status'] === 'pending')->count(),
            'completed_count' => $allPayments->filter(fn ($p) => $p['status'] === 'completed' || $p['status'] === 'cash')->count(),
        ];

        $clients = Client::query()->orderBy('last_name')->orderBy('first_name')->get(['id', 'first_name', 'last_name']);

        return view('admin.payments.all', compact(
            'allPayments',
            'stats',
            'clients',
            'type',
            'status',
            'clientId',
            'search'
        ));
    }
}
