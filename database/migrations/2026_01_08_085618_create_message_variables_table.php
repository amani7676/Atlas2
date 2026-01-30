<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('message_variables', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g., {0}, {1}, {2}
            $table->string('description'); // e.g., "نام اقامتگر", "شماره اتاق"
            $table->string('field_name'); // e.g., "resident_name", "room_number"
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_variables');
    }
};
