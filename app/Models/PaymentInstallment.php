<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentInstallment extends Model
{
    protected $fillable = [
        'payment_plan_id',
        'sequence',
        'type',
        'due_date',
        'amount_due',
        'principal_due',
        'interest_due',
        'amount_paid',
        'penalty_accrued',
        'penalty_paid',
        'status',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'paid_at' => 'date',
            'amount_due' => 'decimal:2',
            'principal_due' => 'decimal:2',
            'interest_due' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'penalty_accrued' => 'decimal:2',
            'penalty_paid' => 'decimal:2',
        ];
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(PaymentPlan::class, 'payment_plan_id');
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(PaymentTransactionAllocation::class);
    }

    public function installmentBalance(): float
    {
        return max(0.0, (float) $this->amount_due - (float) $this->amount_paid);
    }

    public function penaltyBalance(): float
    {
        return max(0.0, (float) $this->penalty_accrued - (float) $this->penalty_paid);
    }

    public function dueDateImmutable(): CarbonImmutable
    {
        return CarbonImmutable::parse($this->due_date);
    }
}

