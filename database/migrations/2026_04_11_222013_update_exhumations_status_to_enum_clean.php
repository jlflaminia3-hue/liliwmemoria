<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This migration has been superseded by the updated create_exhumations_table migration.
     */
    public function up(): void
    {
        // No-op: the workflow_status enum is now created directly in the schema
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
