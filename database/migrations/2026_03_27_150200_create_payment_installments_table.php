<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_plan_id')->constrained('payment_plans')->cascadeOnDelete();

            $table->unsignedSmallInteger('sequence')->default(0)->index(); // 0 = downpayment, then 1..term
            $table->string('type')->default('installment')->index(); // downpayment|installment

            $table->date('due_date')->index();
            $table->decimal('amount_due', 12, 2);
            $table->decimal('principal_due', 12, 2)->default(0);
            $table->decimal('interest_due', 12, 2)->default(0);

            $table->decimal('amount_paid', 12, 2)->default(0);

            $table->decimal('penalty_accrued', 12, 2)->default(0);
            $table->decimal('penalty_paid', 12, 2)->default(0);

            $table->string('status')->default('pending')->index(); // pending|partial|paid|overdue
            $table->date('paid_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_installments');
    }
};
