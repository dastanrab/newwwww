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
        Schema::create('locations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('fava_id')->nullable();
            $table->unsignedBigInteger('car_id')->index('locations_car_id_foreign');
            $table->double('lat');
            $table->double('long');
            $table->integer('speed')->nullable();
            $table->timestamp('date')->nullable()->default('2022-12-08 10:36:46')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
