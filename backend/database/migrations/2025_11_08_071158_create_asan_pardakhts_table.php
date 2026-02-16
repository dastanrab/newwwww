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
        Schema::create('asan_pardakhts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('city_id')->nullable()->default(1)->index('asan_pardakhts_city_id_foreign');
            $table->unsignedBigInteger('fava_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('method')->nullable();
            $table->string('type')->nullable();
            $table->unsignedBigInteger('type_id')->nullable();
            $table->integer('host_id');
            $table->string('host_tran_id');
            $table->string('host_req_time');
            $table->integer('host_opcode');
            $table->integer('status_code');
            $table->unsignedBigInteger('amount');
            $table->unsignedBigInteger('wallet_balance');
            $table->string('settle_token');
            $table->string('rrn');
            $table->string('status_message')->nullable();
            $table->string('details');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asan_pardakhts');
    }
};
