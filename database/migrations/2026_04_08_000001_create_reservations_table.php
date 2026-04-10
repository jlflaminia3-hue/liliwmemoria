<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('lot_id')->constrained('lots')->cascadeOnDelete();
            $table->foreignId('payment_plan_id')->nullable()->constrained('payment_plans')->nullOnDelete();
            $table->date('reserved_at');
            $table->date('expires_at')->nullable();
            $table->string('status')->default('active')->index();
            $table->string('payment_status')->nullable();
            $table->text('payment_terms')->nullable();
            $table->string('contract_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('fulfilled_at')->nullable();
            $table->timestamps();

            $table->index(['lot_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};

