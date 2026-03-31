<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_contracts', function (Blueprint $table) {
            $table->foreignId('created_by_user_id')->nullable()->after('client_id')->constrained('users')->nullOnDelete();
            $table->string('pdf_path')->nullable()->after('notes');
            $table->dateTime('pdf_generated_at')->nullable()->after('pdf_path');
            $table->dateTime('pdf_emailed_at')->nullable()->after('pdf_generated_at');
        });
    }

    public function down(): void
    {
        Schema::table('client_contracts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('created_by_user_id');
            $table->dropColumn(['pdf_path', 'pdf_generated_at', 'pdf_emailed_at']);
        });
    }
};

