<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('lot_id')->nullable()->constrained('lots')->nullOnDelete();
            $table->string('contract_number')->nullable()->index();
            $table->string('contract_type')->default('purchase');
            $table->string('status')->default('draft');
            $table->decimal('total_amount', 12, 2)->nullable();
            $table->decimal('amount_paid', 12, 2)->nullable();
            $table->date('due_date')->nullable();
            $table->date('signed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_contracts');
    }
};

