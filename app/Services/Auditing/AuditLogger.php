<?php

namespace App\Services\Auditing;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    /**
     * @return array<int, string>
     */
    public static function ignoredAttributes(): array
    {
        return [
            'created_at',
            'updated_at',
            'deleted_at',
            'password',
            'remember_token',
        ];
    }

    public static function created(Model $model): void
    {
        self::write(
            event: 'created',
            model: $model,
            oldValues: null,
            newValues: self::filterValues($model->getAttributes()),
        );
    }

    public static function updated(Model $model): void
    {
        $changes = $model->getChanges();
        $changes = Arr::except($changes, self::ignoredAttributes());

        if ($changes === []) {
            return;
        }

        $original = $model->getOriginal();
        $oldValues = Arr::only($original, array_keys($changes));

        self::write(
            event: 'updated',
            model: $model,
            oldValues: self::filterValues($oldValues),
            newValues: self::filterValues($changes),
        );
    }

    public static function deleted(Model $model): void
    {
        self::write(
            event: 'deleted',
            model: $model,
            oldValues: self::filterValues($model->getAttributes()),
            newValues: null,
        );
    }

    public static function login(User $user): void
    {
        self::writeAuthEvent('login', $user);
    }

    public static function logout(User $user): void
    {
        self::writeAuthEvent('logout', $user);
    }

    /**
     * @param  array<string, mixed>|null  $oldValues
     * @param  array<string, mixed>|null  $newValues
     */
    protected static function write(string $event, Model $model, ?array $oldValues, ?array $newValues): void
    {
        if ($model instanceof AuditLog) {
            return;
        }

        $request = request();

        AuditLog::query()->create([
            'event' => $event,
            'auditable_type' => $model->getMorphClass(),
            'auditable_id' => $model->getKey(),
            'user_id' => Auth::id(),
            'url' => $request?->fullUrl(),
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);
    }

    protected static function writeAuthEvent(string $event, User $user): void
    {
        $request = request();

        AuditLog::query()->create([
            'event' => $event,
            'auditable_type' => User::class,
            'auditable_id' => $user->id,
            'user_id' => $user->id,
            'url' => $request?->fullUrl(),
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'old_values' => null,
            'new_values' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }

    /**
     * @param  array<string, mixed>  $values
     * @return array<string, mixed>
     */
    protected static function filterValues(array $values): array
    {
        return Arr::except($values, self::ignoredAttributes());
    }
}

