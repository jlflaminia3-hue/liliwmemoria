<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('client_contracts')
            ->whereIn('status', ['draft', 'past_due'])
            ->update(['status' => 'pending']);
    }

    public function down(): void
    {
        // Intentionally left blank: prior values (draft vs past_due) can't be reconstructed safely.
    }
};

