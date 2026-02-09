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
        Schema::table('contracts', function (Blueprint $table) {
            // Add indexes for better performance
            $table->index('resident_id', 'idx_contracts_resident_id');
            $table->index('bed_id', 'idx_contracts_bed_id');
            $table->index('state', 'idx_contracts_state');
            $table->index('payment_date', 'idx_contracts_payment_date');
            $table->index(['resident_id', 'bed_id'], 'idx_contracts_resident_bed');
            $table->index(['state', 'payment_date'], 'idx_contracts_state_payment_date');
        });

        Schema::table('residents', function (Blueprint $table) {
            // Add indexes for residents table
            $table->index('full_name', 'idx_residents_full_name');
            $table->index('phone', 'idx_residents_phone');
        });

        Schema::table('notes', function (Blueprint $table) {
            // Add indexes for notes table
            $table->index('resident_id', 'idx_notes_resident_id');
            $table->index('type', 'idx_notes_type');
            $table->index(['resident_id', 'type'], 'idx_notes_resident_type');
        });

        Schema::table('beds', function (Blueprint $table) {
            // Add indexes for beds table
            $table->index('room_id', 'idx_beds_room_id');
            $table->index('state', 'idx_beds_state');
        });

        Schema::table('rooms', function (Blueprint $table) {
            // Add indexes for rooms table
            $table->index('unit_id', 'idx_rooms_unit_id');
            $table->index('type', 'idx_rooms_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropIndex('idx_contracts_resident_id');
            $table->dropIndex('idx_contracts_bed_id');
            $table->dropIndex('idx_contracts_state');
            $table->dropIndex('idx_contracts_payment_date');
            $table->dropIndex('idx_contracts_resident_bed');
            $table->dropIndex('idx_contracts_state_payment_date');
        });

        Schema::table('residents', function (Blueprint $table) {
            $table->dropIndex('idx_residents_full_name');
            $table->dropIndex('idx_residents_phone');
        });

        Schema::table('notes', function (Blueprint $table) {
            $table->dropIndex('idx_notes_resident_id');
            $table->dropIndex('idx_notes_type');
            $table->dropIndex('idx_notes_resident_type');
        });

        Schema::table('beds', function (Blueprint $table) {
            $table->dropIndex('idx_beds_room_id');
            $table->dropIndex('idx_beds_state');
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->dropIndex('idx_rooms_unit_id');
            $table->dropIndex('idx_rooms_type');
        });
    }
};
