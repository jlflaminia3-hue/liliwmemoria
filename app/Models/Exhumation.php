<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Exhumation extends Model
{
    use Auditable;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_SUBMITTED = 'submitted';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_SCHEDULED = 'scheduled';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_ARCHIVED = 'archived';

    public const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_SUBMITTED,
        self::STATUS_APPROVED,
        self::STATUS_SCHEDULED,
        self::STATUS_COMPLETED,
        self::STATUS_ARCHIVED,
    ];

    protected $fillable = [
        'deceased_id',
        'workflow_status',
        'requested_by_name',
        'requested_by_relationship',
        'requested_at',
        'approved_at',
        'exhumed_at',
        'notes',

        'exhumation_permit_path',
        'transfer_permit_path',

        'destination_cemetery_name',
        'destination_address',
        'destination_city',
        'destination_province',
        'destination_contact_person',
        'destination_contact_phone',
        'destination_contact_email',

        'transport_company',
        'transport_vehicle_plate',
        'transport_driver_name',
        'transport_log',

        'transfer_certificate_path',
        'transfer_certificate_generated_at',
    ];

    protected function casts(): array
    {
        return [
            'requested_at' => 'datetime',
            'approved_at' => 'datetime',
            'exhumed_at' => 'datetime',
            'transfer_certificate_generated_at' => 'datetime',
        ];
    }

    public function deceased(): BelongsTo
    {
        return $this->belongsTo(Deceased::class);
    }

    public function isRemainsRemovedFromLot(): bool
    {
        return in_array($this->workflow_status, [
            self::STATUS_SCHEDULED,
            self::STATUS_COMPLETED,
        ], true);
    }
}
