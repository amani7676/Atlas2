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
        Schema::create('heaters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->bigInteger('number')->nullable();
            $table->text('desc')->nullable();
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active');
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable()->unique();
            $table->date('installation_date')->nullable();
            $table->bigInteger('room_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('heaters');
    }
};
