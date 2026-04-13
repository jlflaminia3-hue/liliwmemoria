<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deceased', function (Blueprint $table) {
            $table->decimal('interment_fee', 10, 2)->nullable()->after('notes');
            $table->decimal('payment_before_excavation', 10, 2)->nullable()->after('interment_fee');
            $table->decimal('payment_after_interment', 10, 2)->nullable()->after('payment_before_excavation');
            $table->date('payment_before_excavation_date')->nullable()->after('payment_after_interment');
            $table->date('payment_after_interment_date')->nullable()->after('payment_before_excavation_date');
            $table->enum('payment_status', ['unpaid', 'partial', 'fully_paid'])->default('unpaid')->after('payment_after_interment_date');
            $table->boolean('excavation_scheduled')->default(false)->after('payment_status');
            $table->date('excavation_date')->nullable()->after('excavation_scheduled');
            $table->string('contract_path')->nullable()->after('excavation_date');
            $table->string('interment_number')->nullable()->unique()->after('contract_path');
            $table->timestamp('contract_sent_at')->nullable()->after('interment_number');
        });
    }

    public function down(): void
    {
        Schema::table('deceased', function (Blueprint $table) {
            $table->dropColumn([
                'interment_fee',
                'payment_before_excavation',
                'payment_after_interment',
                'payment_before_excavation_date',
                'payment_after_interment_date',
                'payment_status',
                'excavation_scheduled',
                'excavation_date',
                'contract_path',
                'interment_number',
                'contract_sent_at',
            ]);
        });
    }
};
