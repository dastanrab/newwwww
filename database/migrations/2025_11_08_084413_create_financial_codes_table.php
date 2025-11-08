<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_codes', function (Blueprint $table) {
            $table->id(); // شناسه یکتا
            $table->string('status')->default('pending'); // وضعیت
            $table->enum('type', ['deposit', 'withdraw'])->default('deposit'); // نوع تراکنش
            $table->string('title'); // عنوان
            $table->timestamps(); // created_at و updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_codes');
    }
};
