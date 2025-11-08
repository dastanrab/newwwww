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
        Schema::create('polygon_day_hours', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('city_id')->default(1)->index('polygon_day_hours_city_id_foreign');
            $table->unsignedBigInteger('polygon_id')->index('polygon_day_hours_polygon_id_foreign');
            $table->unsignedBigInteger('day_id')->index('polygon_day_hours_day_id_foreign');
            $table->unsignedBigInteger('hour_id')->index('polygon_day_hours_hour_id_foreign');
            $table->boolean('status')->default(true)->index();
            $table->timestamps();

            $table->index(['city_id']);
            $table->index(['day_id']);
            $table->index(['hour_id']);
            $table->index(['polygon_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('polygon_day_hours');
    }
};
