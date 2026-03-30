<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class EnsureMasterAdmin extends Command
{
    protected $signature = 'app:ensure-master-admin {--force-password : Always overwrite the password from env}';

    protected $description = 'Create or update the single master admin account from environment variables.';

    public function handle(): int
    {
        $email = (string) env('MASTER_ADMIN_EMAIL', '');
        $password = (string) env('MASTER_ADMIN_PASSWORD', '');
        $name = (string) env('MASTER_ADMIN_NAME', 'Master Admin');

        if ($email === '' || $password === '') {
            $this->error('Missing MASTER_ADMIN_EMAIL or MASTER_ADMIN_PASSWORD in .env');
            return self::FAILURE;
        }

        $user = User::query()->firstOrNew(['email' => $email]);

        $user->name = $name;
        $user->role = 'master_admin';
        $user->email_verified_at = $user->email_verified_at ?? now();

        if (! $user->exists || (bool) $this->option('force-password')) {
            $user->password = Hash::make($password);
        }

        $user->save();

        $this->info("Master admin ensured: {$user->email} (id: {$user->id})");

        return self::SUCCESS;
    }
}

