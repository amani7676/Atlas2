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
        // Set all existing units to displayable = true
        DB::table('units')->whereNull('is_displayable')->update(['is_displayable' => true]);
        
        // Set all existing rooms to displayable = true
        DB::table('rooms')->whereNull('is_displayable')->update(['is_displayable' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
