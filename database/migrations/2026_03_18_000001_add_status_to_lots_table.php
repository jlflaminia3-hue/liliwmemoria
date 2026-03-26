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
            $table->string('status')->default('available')->after('is_occupied');
        });

        // Backfill from the existing boolean flag.
        DB::table('lots')->where('is_occupied', true)->update(['status' => 'occupied']);
        DB::table('lots')->where('is_occupied', false)->update(['status' => 'available']);
    }

    public function down(): void
    {
        Schema::table('lots', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
