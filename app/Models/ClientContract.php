<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClientContract extends Model
{
    use Auditable;

    public static function formatContractNumber(int $id): string
    {
        return 'CN-' . str_pad((string) $id, 6, '0', STR_PAD_LEFT);
    }

    protected $fillable = [
        'client_id',
        'created_by_user_id',
        'lot_id',
        'lot_kind',
        'contract_number',
        'contract_type',
        'status',
        'total_amount',
        'amount_paid',
        'due_date',
        'signed_at',
        'contract_duration_months',
        'notes',
        'pdf_path',
        'pdf_generated_at',
        'pdf_emailed_at',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'due_date' => 'date',
            'signed_at' => 'date',
            'contract_duration_months' => 'integer',
            'pdf_generated_at' => 'datetime',
            'pdf_emailed_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }

    public function paymentPlans(): HasMany
    {
        return $this->hasMany(PaymentPlan::class, 'client_contract_id');
    }
}
