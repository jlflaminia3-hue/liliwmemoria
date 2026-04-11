<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exhumations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deceased_id')->constrained('deceased')->cascadeOnDelete();

            $table->enum('workflow_status', ['draft', 'submitted', 'approved', 'scheduled', 'completed', 'archived'])->default('draft');
            $table->string('requested_by_name')->nullable();
            $table->string('requested_by_relationship')->nullable();
            $table->dateTime('requested_at')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->dateTime('exhumed_at')->nullable();
            $table->text('notes')->nullable();

            $table->string('exhumation_permit_path')->nullable();
            $table->string('transfer_permit_path')->nullable();

            $table->string('destination_cemetery_name')->nullable();
            $table->string('destination_address')->nullable();
            $table->string('destination_city')->nullable();
            $table->string('destination_province')->nullable();
            $table->string('destination_contact_person')->nullable();
            $table->string('destination_contact_phone')->nullable();
            $table->string('destination_contact_email')->nullable();

            $table->string('transport_company')->nullable();
            $table->string('transport_vehicle_plate')->nullable();
            $table->string('transport_driver_name')->nullable();
            $table->text('transport_log')->nullable();

            $table->string('transfer_certificate_path')->nullable();
            $table->dateTime('transfer_certificate_generated_at')->nullable();

            $table->timestamps();

            $table->index(['workflow_status', 'requested_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exhumations');
    }
};
