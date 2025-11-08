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
        Schema::table('polygon_day_hours', function (Blueprint $table) {
            $table->foreign(['city_id'])->references(['id'])->on('cities')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['day_id'])->references(['id'])->on('days')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['hour_id'])->references(['id'])->on('hours')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['polygon_id'])->references(['id'])->on('polygons')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('polygon_day_hours', function (Blueprint $table) {
            $table->dropForeign('polygon_day_hours_city_id_foreign');
            $table->dropForeign('polygon_day_hours_day_id_foreign');
            $table->dropForeign('polygon_day_hours_hour_id_foreign');
            $table->dropForeign('polygon_day_hours_polygon_id_foreign');
        });
    }
};
