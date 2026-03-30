<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lot extends Model
{
    use Auditable;

    protected $fillable = [
        'lot_number',
        'name',
        'section',
        'latitude',
        'longitude',
        'geometry_type',
        'geometry',
        'is_occupied',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_occupied' => 'boolean',
            'lot_number' => 'integer',
            'latitude' => 'float',
            'longitude' => 'float',
            'geometry' => 'array',
        ];
    }

    public function deceased(): HasMany
    {
        return $this->hasMany(Deceased::class);
    }
}
