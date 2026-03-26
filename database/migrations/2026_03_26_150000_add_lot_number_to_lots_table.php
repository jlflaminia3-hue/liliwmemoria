<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lots', function (Blueprint $table) {
            $table->unsignedBigInteger('lot_number')->nullable()->after('id');
        });

        // Backfill existing records with a stable unique value.
        DB::table('lots')->whereNull('lot_number')->update([
            'lot_number' => DB::raw('id'),
        ]);

        Schema::table('lots', function (Blueprint $table) {
            $table->unique('lot_number');
        });
    }

    public function down(): void
    {
        Schema::table('lots', function (Blueprint $table) {
            $table->dropUnique(['lot_number']);
            $table->dropColumn('lot_number');
        });
    }
};

