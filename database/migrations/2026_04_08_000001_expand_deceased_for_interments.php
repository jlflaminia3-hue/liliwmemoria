<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deceased', function (Blueprint $table) {
            $table->foreignId('client_id')->nullable()->after('lot_id')->constrained()->nullOnDelete();
            $table->string('status')->default('pending')->after('burial_date');
            $table->string('death_certificate_path')->nullable()->after('status');
            $table->string('burial_permit_path')->nullable()->after('death_certificate_path');
            $table->string('interment_form_path')->nullable()->after('burial_permit_path');
            $table->index(['status', 'burial_date']);
        });
    }

    public function down(): void
    {
        Schema::table('deceased', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropIndex(['status', 'burial_date']);
            $table->dropColumn([
                'client_id',
                'status',
                'death_certificate_path',
                'burial_permit_path',
                'interment_form_path',
            ]);
        });
    }
};
