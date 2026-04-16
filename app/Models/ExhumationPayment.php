<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExhumationPayment extends Model
{
    use Auditable;

    protected $fillable = [
        'exhumation_id',
        'amount',
        'payment_date',
        'method',
        'reference_number',
        'notes',
        'receipt_path',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function exhumation(): BelongsTo
    {
        return $this->belongsTo(Exhumation::class);
    }
}
