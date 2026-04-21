<?php

namespace App\Models\Concerns;

use App\Services\Auditing\AuditLogger;
use Illuminate\Database\Eloquent\Model;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function (Model $model): void {
            AuditLogger::created($model);
        });

        static::updated(function (Model $model): void {
            AuditLogger::updated($model);
        });

        static::deleted(function (Model $model): void {
            AuditLogger::deleted($model);
        });
    }
}
