<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisitorLog extends Model
{
    protected $fillable = [
        'deceased_id',
        'visitor_name',
        'contact_number',
        'address',
        'purpose',
        'visited_at',
    ];

    protected function casts(): array
    {
        return [
            'visited_at' => 'datetime',
        ];
    }

    public function deceased(): BelongsTo
    {
        return $this->belongsTo(Deceased::class);
    }
}

