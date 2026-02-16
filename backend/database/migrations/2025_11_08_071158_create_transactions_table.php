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
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('s_user_id')->nullable();
            $table->integer('d_user_id')->nullable();
            $table->double('amount')->nullable();
            $table->tinyInteger('type')->nullable();
            $table->integer('transactionable_id')->nullable();
            $table->string('transactionable_type')->nullable();
            $table->integer('pay_type')->nullable();
            $table->integer('reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
