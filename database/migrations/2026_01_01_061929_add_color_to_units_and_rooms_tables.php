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
        Schema::table('units', function (Blueprint $table) {
            $table->string('color', 7)->nullable()->after('desc');
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->string('color', 7)->nullable()->after('desc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->dropColumn('color');
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
};
