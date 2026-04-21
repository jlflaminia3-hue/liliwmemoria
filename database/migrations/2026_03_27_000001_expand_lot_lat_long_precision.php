<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Supports image-coordinate values like 2004.43 while keeping room for decimals.
        DB::statement('ALTER TABLE `lots` MODIFY `latitude` DECIMAL(12,8) NOT NULL');
        DB::statement('ALTER TABLE `lots` MODIFY `longitude` DECIMAL(13,8) NOT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE `lots` MODIFY `latitude` DECIMAL(10,8) NOT NULL');
        DB::statement('ALTER TABLE `lots` MODIFY `longitude` DECIMAL(11,8) NOT NULL');
    }
};
