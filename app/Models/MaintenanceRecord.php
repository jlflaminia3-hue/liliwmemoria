<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceRecord extends Model
{
    use Auditable;

    protected $fillable = [
        'client_id',
        'lot_id',
        'client_contract_id',
        'service_type',
        'status',
        'service_date',
        'amount',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'service_date' => 'date',
            'amount' => 'decimal:2',
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

    public function contract(): BelongsTo
    {
        return $this->belongsTo(ClientContract::class, 'client_contract_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

