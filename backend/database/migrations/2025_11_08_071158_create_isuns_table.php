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
        Schema::create('isuns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->index('isuns_user_id_foreign');
            $table->integer('amount');
            $table->unsignedBigInteger('isun_id');
            $table->string('operator');
            $table->string('receive_address');
            $table->string('requester_address');
            $table->string('subscriber_no');
            $table->string('topup_type');
            $table->string('trace_code');
            $table->string('transaction_id');
            $table->string('package_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('isuns');
    }
};
