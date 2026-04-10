<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deceased extends Model
{
    use Auditable;

    protected $table = 'deceased';

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
        ];
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
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
}
