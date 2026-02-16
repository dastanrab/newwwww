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
        Schema::create('cashouts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('fava_id')->nullable();
            $table->unsignedBigInteger('user_id')->index('cashouts_user_id_foreign');
            $table->integer('amount');
            $table->string('name')->nullable();
            $table->string('card_number')->nullable();
            $table->string('shaba_number')->nullable();
            $table->string('trace_code')->nullable();
            $table->string('status')->nullable()->index();
            $table->string('bank')->nullable();
            $table->text('bank_id')->nullable();
            $table->unsignedBigInteger('operator_id')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('submit_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashouts');
    }
};
