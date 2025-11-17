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
        Schema::create('residents', function (Blueprint $table) {
             $table->id();
            $table->string('full_name');
            $table->string('phone')->nullable();
            $table->integer('age')->nullable()->default(0);
            $table->date('birth_date')->nullable();
            $table->enum('job', [
                'daneshjo_dolati',
                'daneshjo_azad',
                'daneshjo_other',
                'karmand_dolat',
                'karmand_shakhse',
                'azad',
                'nurse',
                'other'
            ])->nullable();
            $table->enum('referral_source', [
                'university_introduction',
                'university_website',
                'google',
                'map',
                'khobinja_website',
                'introducing_friends',
                'street',
                'divar',
                'other'
            ])->comment('نحوه اشنایی')->nullable();
            $table->boolean('form')->default(false);
            $table->boolean('document')->default(false);
            $table->boolean('rent')->default(false);
            $table->boolean('trust')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('residents');
    }
};
