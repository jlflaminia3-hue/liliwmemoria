<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IntermentPayment extends Model
{
    use Auditable;

    protected $fillable = [
        'deceased_id',
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

    public function deceased(): BelongsTo
    {
        return $this->belongsTo(Deceased::class);
    }
}
