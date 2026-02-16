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
        Schema::create('phoneusers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedTinyInteger('gender')->nullable();
            $table->string('name')->nullable();
            $table->string('lastname')->nullable();
            $table->boolean('legal')->nullable();
            $table->unsignedBigInteger('guild_id')->nullable();
            $table->string('guild_title')->nullable();
            $table->string('mobile')->nullable();
            $table->string('phone')->nullable();
            $table->unsignedBigInteger('card_number')->nullable();
            $table->string('shaba_number')->nullable();
            $table->string('cardholder')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phoneusers');
    }
};
