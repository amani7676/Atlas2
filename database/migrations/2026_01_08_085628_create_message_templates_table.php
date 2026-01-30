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
        Schema::create('message_templates', function (Blueprint $table) {
            $table->id();
            $table->integer('body_id')->unique(); // BodyID from API
            $table->string('title'); // Title from API
            $table->text('body'); // Body content with variables
            $table->dateTime('insert_date'); // InsertDate from API
            $table->integer('body_status'); // BodyStatus from API
            $table->text('description')->nullable(); // Description from API
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_templates');
    }
};
