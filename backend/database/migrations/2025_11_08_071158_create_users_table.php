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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('fava_id')->nullable();
            $table->string('paygear_id')->nullable();
            $table->boolean('legal')->default(false)->index('users_legal');
            $table->unsignedBigInteger('guild_id')->nullable()->index('users_guild_id_foreign');
            $table->string('guild_title')->nullable();
            $table->string('name')->nullable();
            $table->string('lastname')->nullable();
            $table->unsignedTinyInteger('gender')->nullable();
            $table->date('birthday')->nullable();
            $table->string('national_code')->nullable();
            $table->unsignedMediumInteger('referral_code')->nullable();
            $table->string('province')->nullable();
            $table->string('region')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('mobile')->unique();
            $table->unsignedBigInteger('card_number')->nullable();
            $table->string('shaba_number')->nullable();
            $table->string('cardholder')->nullable();
            $table->integer('level')->default(1);
            $table->string('password');
            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->rememberToken();
            $table->timestamp('created_at')->nullable()->index();
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
            $table->unsignedBigInteger('city_id')->nullable()->index();
            $table->integer('score')->default(0)->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
