<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $rows = DB::table('client_contracts')->orderBy('id')->get(['id']);
        foreach ($rows as $row) {
            $number = 'CN-' . str_pad((string) $row->id, 6, '0', STR_PAD_LEFT);
            DB::table('client_contracts')->where('id', $row->id)->update(['contract_number' => $number]);
        }

        Schema::table('client_contracts', function (Blueprint $table) {
            $table->unique('contract_number');
        });
    }

    public function down(): void
    {
        Schema::table('client_contracts', function (Blueprint $table) {
            $table->dropUnique(['contract_number']);
        });
    }
};

