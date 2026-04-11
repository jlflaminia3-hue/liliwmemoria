<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitor_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deceased_id')->constrained('deceased')->cascadeOnDelete();
            $table->string('visitor_name');
            $table->string('contact_number')->nullable();
            $table->string('address')->nullable();
            $table->string('purpose')->nullable();
            $table->timestamp('visited_at')->useCurrent();
            $table->timestamps();

            $table->index(['deceased_id', 'visited_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitor_logs');
    }
};

