<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Deceased extends Model
{
    use Auditable;

    protected $table = 'deceased';

    public const PAYMENT_STATUS_UNPAID = 'unpaid';

    public const PAYMENT_STATUS_PARTIAL = 'partial';

    public const PAYMENT_STATUS_FULLY_PAID = 'fully_paid';

    public const PAYMENT_STATUSES = [
        self::PAYMENT_STATUS_UNPAID,
        self::PAYMENT_STATUS_PARTIAL,
        self::PAYMENT_STATUS_FULLY_PAID,
    ];

    public const MAX_INTERMENTS_PER_LOT = 3;

    public const MIN_YEARS_BETWEEN_INTERMENTS = 10;

    public const INTERMENT_FEE_TOTAL = 15000.00;

    public const INTERMENT_FEE_BEFORE_EXCAVATION = 7500.00;

    public const INTERMENT_FEE_AFTER_INTERMENT = 7500.00;

    protected $fillable = [
        'lot_id',
        'client_id',
        'first_name',
        'last_name',
        'date_of_birth',
        'date_of_death',
        'burial_date',
        'status',
        'death_certificate_path',
        'burial_permit_path',
        'interment_form_path',
        'notes',
        'interment_fee',
        'excavation_scheduled',
        'excavation_date',
        'contract_path',
        'interment_number',
        'contract_sent_at',
    ];

    protected $appends = [
        'full_name',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'date_of_death' => 'date',
            'burial_date' => 'date',
            'excavation_date' => 'date',
            'contract_sent_at' => 'datetime',
            'excavation_scheduled' => 'boolean',
            'interment_fee' => 'decimal:2',
        ];
    }

    public function payments(): HasMany
    {
        return $this->hasMany(IntermentPayment::class)->orderBy('payment_date', 'desc');
    }

    public function getTotalPaidAttribute(): float
    {
        return (float) $this->payments->sum('amount');
    }

    public function getPaymentStatusAttribute(): string
    {
        $fee = (float) ($this->interment_fee ?? self::INTERMENT_FEE_TOTAL);
        $paid = $this->total_paid;

        if ($paid >= $fee) {
            return self::PAYMENT_STATUS_FULLY_PAID;
        }
        if ($paid > 0) {
            return self::PAYMENT_STATUS_PARTIAL;
        }

        return self::PAYMENT_STATUS_UNPAID;
    }

    public function getRemainingBalanceAttribute(): float
    {
        $fee = (float) ($this->interment_fee ?? self::INTERMENT_FEE_TOTAL);

        return max(0, $fee - $this->total_paid);
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function exhumations(): HasMany
    {
        return $this->hasMany(Exhumation::class);
    }

    public function latestExhumation(): HasOne
    {
        return $this->hasOne(Exhumation::class)->latestOfMany();
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function missingComplianceItems(): array
    {
        $missing = [];

        if (! $this->client_id) {
            $missing[] = 'Client link';
        }

        if (! $this->burial_date) {
            $missing[] = 'Interment date';
        }

        if (! $this->death_certificate_path) {
            $missing[] = 'Death certificate';
        }

        if ($this->status === 'confirmed' && ! $this->burial_permit_path) {
            $missing[] = 'Burial permit';
        }

        return $missing;
    }

    public function isComplianceReady(): bool
    {
        return $this->missingComplianceItems() === [];
    }

    public static function getIntermentCountForLot(int $lotId): int
    {
        return static::query()
            ->where('lot_id', $lotId)
            ->where('status', '!=', 'exhumed')
            ->count();
    }

    public static function canAddInterment(int $lotId): array
    {
        $count = static::getIntermentCountForLot($lotId);

        if ($count >= self::MAX_INTERMENTS_PER_LOT) {
            return [
                'allowed' => false,
                'reason' => 'Maximum interments ('.self::MAX_INTERMENTS_PER_LOT.') reached for this lot.',
            ];
        }

        $lastInterment = static::query()
            ->where('lot_id', $lotId)
            ->where('status', '!=', 'exhumed')
            ->whereNotNull('burial_date')
            ->orderByDesc('burial_date')
            ->first();

        if ($lastInterment && $lastInterment->burial_date) {
            $yearsSinceLastInterment = (int) $lastInterment->burial_date->diffInYears(Carbon::today());
            if ($yearsSinceLastInterment < self::MIN_YEARS_BETWEEN_INTERMENTS) {
                $nextEligibleDate = $lastInterment->burial_date->addYears(self::MIN_YEARS_BETWEEN_INTERMENTS);

                return [
                    'allowed' => false,
                    'reason' => 'Minimum '.self::MIN_YEARS_BETWEEN_INTERMENTS.'-year gap required. Next eligible date: '.$nextEligibleDate->format('F d, Y'),
                ];
            }
        }

        return [
            'allowed' => true,
            'interment_count' => $count,
            'max_interments' => self::MAX_INTERMENTS_PER_LOT,
        ];
    }

    public static function getNextEligibleDate(int $lotId): ?Carbon
    {
        $lastInterment = static::query()
            ->where('lot_id', $lotId)
            ->where('status', '!=', 'exhumed')
            ->whereNotNull('burial_date')
            ->orderByDesc('burial_date')
            ->first();

        if (! $lastInterment || ! $lastInterment->burial_date) {
            return null;
        }

        return $lastInterment->burial_date->addYears(self::MIN_YEARS_BETWEEN_INTERMENTS);
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            self::PAYMENT_STATUS_UNPAID => 'Unpaid',
            self::PAYMENT_STATUS_PARTIAL => 'Partial',
            self::PAYMENT_STATUS_FULLY_PAID => 'Fully Paid',
            default => 'Unknown',
        };
    }

    public function getPaymentProgressAttribute(): int
    {
        $total = (float) ($this->interment_fee ?? self::INTERMENT_FEE_TOTAL);
        if ($total <= 0) {
            return 100;
        }

        return (int) min(100, round(($this->total_paid / $total) * 100));
    }

    public static function generateIntermentNumber(): string
    {
        $year = date('Y');
        $count = static::whereYear('created_at', $year)->count() + 1;

        return 'INT-'.$year.'-'.str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
