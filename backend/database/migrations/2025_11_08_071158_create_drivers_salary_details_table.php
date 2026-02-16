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
        Schema::create('drivers_salary_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->double('total_attendance')->default(0);
            $table->double('metals_reward')->default(0);
            $table->double('weight')->nullable()->default(0);
            $table->tinyInteger('salary_type')->nullable()->default(0);
            $table->integer('creator_id')->nullable()->default(0);
            $table->double('weight_price')->default(0);
            $table->double('reward_price')->nullable();
            $table->double('distance')->default(0);
            $table->timestamps();
            $table->unsignedBigInteger('submit_id')->nullable();
            $table->string('detail')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers_salary_details');
    }
};
