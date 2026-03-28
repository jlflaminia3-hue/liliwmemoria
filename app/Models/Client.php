<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'address_line1',
        'address_line2',
        'barangay',
        'city',
        'province',
        'postal_code',
        'country',
        'notes',
    ];

    protected $appends = ['full_name'];

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function lotOwnerships(): HasMany
    {
        return $this->hasMany(ClientLotOwnership::class);
    }

    public function lots(): BelongsToMany
    {
        return $this
            ->belongsToMany(Lot::class, 'client_lot_ownerships')
            ->withPivot(['ownership_type', 'started_at', 'ended_at', 'notes'])
            ->withTimestamps();
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(ClientContract::class);
    }

    public function communications(): HasMany
    {
        return $this->hasMany(ClientCommunication::class);
    }

    public function familyLinks(): HasMany
    {
        return $this->hasMany(ClientFamilyLink::class);
    }

    public function paymentPlans(): HasMany
    {
        return $this->hasMany(PaymentPlan::class);
    }
}
