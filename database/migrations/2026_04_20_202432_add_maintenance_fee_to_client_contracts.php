<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_contracts', function (Blueprint $table) {
            $table->decimal('maintenance_fee', 12, 2)->nullable()->after('amount_paid');
            $table->date('maintenance_fee_due_date')->nullable()->after('maintenance_fee');
            $table->enum('maintenance_fee_status', ['pending', 'paid', 'overdue'])->nullable()->default('pending')->after('maintenance_fee_due_date');
            $table->date('last_maintenance_fee_paid_at')->nullable()->after('maintenance_fee_status');
        });
    }

    public function down(): void
    {
        Schema::table('client_contracts', function (Blueprint $table) {
            $table->dropColumn([
                'maintenance_fee',
                'maintenance_fee_due_date',
                'maintenance_fee_status',
                'last_maintenance_fee_paid_at',
            ]);
        });
    }
};
