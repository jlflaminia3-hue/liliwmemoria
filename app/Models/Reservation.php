<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    use Auditable;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_EXPIRED = 'expired';

    public const STATUS_FULFILLED = 'fulfilled';

    public const PAYMENT_STATUSES = [
        'cash',
        'installment',
    ];

    protected $fillable = [
        'client_id',
        'lot_id',
        'payment_plan_id',
        'client_contract_id',
        'reserved_at',
        'expires_at',
        'status',
        'payment_status',
        'payment_terms',
        'contract_path',
        'notes',
        'fulfilled_at',
    ];

    protected function casts(): array
    {
        return [
            'reserved_at' => 'date',
            'expires_at' => 'date',
            'fulfilled_at' => 'datetime',
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

    public function paymentPlan(): BelongsTo
    {
        return $this->belongsTo(PaymentPlan::class, 'payment_plan_id');
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(ClientContract::class, 'client_contract_id');
    }

    public function isExpired(?CarbonImmutable $today = null): bool
    {
        if ($this->status !== self::STATUS_ACTIVE) {
            return false;
        }

        if (! $this->expires_at) {
            return false;
        }

        $today = $today ?? CarbonImmutable::today();

        return CarbonImmutable::parse($this->expires_at)->lt($today);
    }

    public function scopeActive($query, ?CarbonImmutable $today = null)
    {
        $today = $today ?? CarbonImmutable::today();

        return $query
            ->where('status', self::STATUS_ACTIVE)
            ->where(function ($q) use ($today) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', $today->toDateString());
            });
    }

    /**
     * Expires all active reservations that have passed their expiry date.
     *
     * @return array<int> lot ids affected
     */
    public static function expireDue(?CarbonImmutable $today = null): array
    {
        $today = $today ?? CarbonImmutable::today();

        $due = self::query()
            ->where('status', self::STATUS_ACTIVE)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', $today->toDateString())
            ->get(['id', 'lot_id']);

        if ($due->isEmpty()) {
            return [];
        }

        $ids = $due->pluck('id')->all();
        $lotIds = $due->pluck('lot_id')->unique()->values()->all();

        self::query()
            ->whereIn('id', $ids)
            ->update(['status' => self::STATUS_EXPIRED]);

        return $lotIds;
    }
}
