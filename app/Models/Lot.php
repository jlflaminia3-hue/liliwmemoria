<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\Statusable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lot extends Model
{
    use Auditable;
    use Statusable;

    protected $fillable = [
        'lot_number',
        'name',
        'section',
        'block',
        'latitude',
        'longitude',
        'geometry_type',
        'geometry',
        'is_occupied',
        'status',
        'notes',
    ];

    protected static function booted(): void
    {
        static::creating(function (Lot $lot): void {
            $lot->status ??= self::STATUS_ACTIVE;
        });
    }

    protected $appends = [
        'lot_id',
        'lot_category_label',
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

    public function getLotIdAttribute(): string
    {
        $prefix = self::categoryPrefix((string) ($this->section ?? ''));
        $number = (int) ($this->lot_number ?? 0);

        if ($number <= 0) {
            return $prefix;
        }

        return $prefix.'-'.$number;
    }

    public static function categoryPrefix(string $category): string
    {
        return match ($category) {
            'phase_1' => 'P1',
            'phase_2' => 'P2',
            'garden_lot' => 'G',
            'back_office_lot' => 'BO',
            'narra' => 'N',
            'mausoleum' => 'M',
            default => 'L',
        };
    }

    public static function categoryFromPrefix(string $prefix): ?string
    {
        $p = strtoupper(trim($prefix));

        return match ($p) {
            'P1' => 'phase_1',
            'P2' => 'phase_2',
            'G' => 'garden_lot',
            'BO' => 'back_office_lot',
            'N' => 'narra',
            'M' => 'mausoleum',
            default => null,
        };
    }

    /**
     * Parse a Lot ID like "P1-42" into ['section' => 'phase_1', 'lot_number' => 42].
     */
    public static function parseLotId(string $lotId): ?array
    {
        $value = strtoupper(trim($lotId));
        if ($value === '') {
            return null;
        }

        $parts = explode('-', $value, 2);
        if (count($parts) !== 2) {
            return null;
        }

        [$prefix, $number] = $parts;
        $category = self::categoryFromPrefix($prefix);
        $num = (int) preg_replace('/[^0-9]/', '', $number);

        if (! $category || $num < 1) {
            return null;
        }

        return [
            'section' => $category,
            'lot_number' => $num,
        ];
    }

    public function getLotCategoryLabelAttribute(): string
    {
        return self::categoryLabel((string) ($this->section ?? ''));
    }

    public static function categoryLabel(string $category): string
    {
        return match ($category) {
            'phase_1' => 'Phase 1',
            'phase_2' => 'Phase 2',
            'garden_lot' => 'Garden Lot',
            'back_office_lot' => 'Back Office Lot',
            'narra' => 'Narra',
            'mausoleum' => 'Mausoleum',
            default => $category !== '' ? $category : 'N/A',
        };
    }

    public function deceased(): HasMany
    {
        return $this->hasMany(Deceased::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function lotOwnerships(): HasMany
    {
        return $this->hasMany(ClientLotOwnership::class);
    }
}
