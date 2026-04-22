<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait Statusable
{
    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    public const STATUS_ARCHIVED = 'archived';

    public const STATUS_CANCELLED = 'cancelled';

    public static function getStatusableDefaults(): array
    {
        return [
            self::STATUS_ACTIVE,
            self::STATUS_INACTIVE,
            self::STATUS_ARCHIVED,
            self::STATUS_CANCELLED,
        ];
    }

    public static function getStatusLabel(string $status): string
    {
        return match ($status) {
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_ARCHIVED => 'Archived',
            self::STATUS_CANCELLED => 'Cancelled',
            default => ucfirst($status),
        };
    }

    public static function getStatusBadgeClass(string $status): string
    {
        return match ($status) {
            self::STATUS_ACTIVE => 'bg-success',
            self::STATUS_INACTIVE => 'bg-secondary',
            self::STATUS_ARCHIVED => 'bg-warning text-dark',
            self::STATUS_CANCELLED => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_INACTIVE);
    }

    public function scopeArchived(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ARCHIVED);
    }

    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    public function scopeWithStatus(Builder $query, ?string $status): Builder
    {
        if (blank($status)) {
            return $query;
        }

        return $query->where('status', $status);
    }

    public function scopeSearchable(Builder $query): Builder
    {
        return $query->whereIn('status', [
            self::STATUS_ACTIVE,
            self::STATUS_INACTIVE,
        ]);
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isInactive(): bool
    {
        return $this->status === self::STATUS_INACTIVE;
    }

    public function isArchived(): bool
    {
        return $this->status === self::STATUS_ARCHIVED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function archive(): bool
    {
        return $this->updateQuietly(['status' => self::STATUS_ARCHIVED]);
    }

    public function deactivate(): bool
    {
        return $this->updateQuietly(['status' => self::STATUS_INACTIVE]);
    }

    public function activate(): bool
    {
        return $this->updateQuietly(['status' => self::STATUS_ACTIVE]);
    }
}
