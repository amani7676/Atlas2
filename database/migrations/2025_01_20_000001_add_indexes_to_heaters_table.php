<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('heaters', function (Blueprint $table) {
            // Add indexes for better query performance
            $table->index('status');
            $table->index('room_id');
            $table->index('name');
            $table->index('number');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('heaters', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['room_id']);
            $table->dropIndex(['name']);
            $table->dropIndex(['number']);
            $table->dropIndex(['created_at']);
        });
    }
};
