<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PaymentPlan extends Model
{
    use Auditable;

    protected $fillable = [
        'client_id',
        'client_contract_id',
        'lot_id',
        'plan_number',
        'status',
        'principal_amount',
        'downpayment_amount',
        'term_months',
        'interest_rate_percent',
        'financed_principal',
        'interest_amount',
        'start_date',
        'penalty_grace_days',
        'penalty_rate_percent',
        'notes',
        'last_notified_at',
    ];

    protected function casts(): array
    {
        return [
            'principal_amount' => 'decimal:2',
            'downpayment_amount' => 'decimal:2',
            'interest_rate_percent' => 'decimal:2',
            'financed_principal' => 'decimal:2',
            'interest_amount' => 'decimal:2',
            'start_date' => 'date',
            'penalty_rate_percent' => 'decimal:2',
            'last_notified_at' => 'datetime',
        ];
    }

    public static function interestRateForTerm(int $termMonths): float
    {
        return match ($termMonths) {
            12 => 10.0,
            18 => 15.0,
            24 => 20.0,
            default => 0.0,
        };
    }

    public static function generatePlanNumber(): string
    {
        return 'PAY-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(ClientContract::class, 'client_contract_id');
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }

    public function installments(): HasMany
    {
        return $this->hasMany(PaymentInstallment::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function startDateImmutable(): CarbonImmutable
    {
        return CarbonImmutable::parse($this->start_date);
    }
}
