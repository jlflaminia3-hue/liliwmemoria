<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_contracts', function (Blueprint $table) {
            $table->string('lot_kind')->nullable()->after('lot_id');
            $table->unsignedSmallInteger('contract_duration_months')->nullable()->after('signed_at');
        });
    }

    public function down(): void
    {
        Schema::table('client_contracts', function (Blueprint $table) {
            $table->dropColumn(['lot_kind', 'contract_duration_months']);
        });
    }
};
