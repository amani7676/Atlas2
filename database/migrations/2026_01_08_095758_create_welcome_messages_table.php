<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('welcome_messages', function (Blueprint $table) {
            $table->id();
            $table->string('body_id')->comment('کد الگوی پیام');
            $table->date('send_date')->comment('تاریخ ارسال');
            $table->boolean('is_active')->default(true)->comment('آیا پیام فعال است');
            $table->timestamps();
            
            $table->index('body_id');
            $table->index('send_date');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('welcome_messages');
    }
};
