<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lot extends Model
{
    protected $fillable = [
        'name',
        'section',
        'latitude',
        'longitude',
        'is_occupied',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_occupied' => 'boolean',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function deceased(): HasMany
    {
        return $this->hasMany(Deceased::class);
    }
}
