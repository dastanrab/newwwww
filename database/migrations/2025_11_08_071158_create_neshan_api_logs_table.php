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
        Schema::create('neshan_api_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('start_info')->nullable();
            $table->unsignedBigInteger('is_driver_location')->nullable();
            $table->string('endpoint');
            $table->text('request_data')->nullable();
            $table->text('response_data')->nullable();
            $table->integer('status_code')->nullable();
            $table->string('error_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('neshan_api_logs');
    }
};
