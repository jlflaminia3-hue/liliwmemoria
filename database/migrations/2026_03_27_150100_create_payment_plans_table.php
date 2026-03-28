<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('client_contract_id')->nullable()->constrained('client_contracts')->nullOnDelete();
            $table->foreignId('lot_id')->nullable()->constrained('lots')->nullOnDelete();

            $table->string('plan_number')->nullable()->index();
            $table->string('status')->default('active')->index(); // active|completed|canceled

            $table->decimal('principal_amount', 12, 2);
            $table->decimal('downpayment_amount', 12, 2)->default(0);

            $table->unsignedSmallInteger('term_months'); // 12|18|24
            $table->decimal('interest_rate_percent', 5, 2); // 10.00, 15.00, 20.00
            $table->decimal('financed_principal', 12, 2)->default(0);
            $table->decimal('interest_amount', 12, 2)->default(0);

            $table->date('start_date');

            $table->unsignedSmallInteger('penalty_grace_days')->default(0);
            $table->decimal('penalty_rate_percent', 5, 2)->default(0); // per 30 days overdue, applied to unpaid installment balance

            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_plans');
    }
};

