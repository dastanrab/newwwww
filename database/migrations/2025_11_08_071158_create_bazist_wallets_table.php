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
        Schema::create('bazist_wallets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('city_id')->nullable()->default(1);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('wallet_id')->nullable();
            $table->string('type')->nullable();
            $table->unsignedBigInteger('type_id')->nullable();
            $table->unsignedBigInteger('amount');
            $table->unsignedBigInteger('wallet_balance');
            $table->string('method')->nullable();
            $table->string('details')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('operator_id')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bazist_wallets');
    }
};
