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
        Schema::create('archive_data', function (Blueprint $table) {
            $table->id();
            
            // Resident fields
            $table->string('full_name');
            $table->string('phone')->nullable();
            $table->integer('age')->nullable();
            $table->string('job')->nullable();
            $table->string('referral_source')->nullable();
            $table->boolean('form')->default(false);
            $table->boolean('rent')->default(false);
            $table->boolean('trust')->default(false);
            $table->boolean('document')->default(false);
            $table->date('birth_date')->nullable();
            
            // Contract fields
            $table->date('payment_date')->nullable();
            $table->integer('bed_id')->nullable();
            $table->string('state')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            
            // Additional info
            $table->string('room_name')->nullable();
            $table->string('bed_name')->nullable();
            $table->string('unit_name')->nullable();
            
            // Archive timestamp
            $table->timestamp('archived_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archive_data');
    }
};
