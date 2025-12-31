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
        Schema::table('rezerves', function (Blueprint $table) {
            $table->dropForeign(['bed_id']);
            $table->foreignId('bed_id')->nullable()->change();
            $table->foreign('bed_id')->references('id')->on('beds')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rezerves', function (Blueprint $table) {
            $table->dropForeign(['bed_id']);
            $table->foreignId('bed_id')->nullable(false)->change();
            $table->foreign('bed_id')->references('id')->on('beds')->onUpdate('cascade')->onDelete('cascade');
        });
    }
};
