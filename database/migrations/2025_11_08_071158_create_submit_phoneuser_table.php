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
        Schema::create('submit_phoneuser', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('submit_id')->index('submit_phoneuser_submit_id_foreign');
            $table->unsignedBigInteger('user_id')->nullable()->index('submit_phoneuser_user_id_foreign');
            $table->unsignedBigInteger('phoneuser_id')->nullable()->index('submit_phoneuser_phoneuser_id_foreign');
            $table->string('trace_code')->nullable();
            $table->timestamp('pay_time')->nullable();
            $table->unsignedBigInteger('operator_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submit_phoneuser');
    }
};
