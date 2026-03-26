<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lot extends Model
{
    protected $fillable = [
        'lot_number',
        'name',
        'section',
        'latitude',
        'longitude',
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
        ];
    }

    public function deceased(): HasMany
    {
        return $this->hasMany(Deceased::class);
    }
}
