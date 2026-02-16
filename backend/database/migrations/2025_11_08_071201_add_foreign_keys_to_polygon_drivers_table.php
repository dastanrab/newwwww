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
        Schema::table('polygon_drivers', function (Blueprint $table) {
            $table->foreign(['polygon_id'], 'polygon_pakbans_polygon_id_foreign')->references(['id'])->on('polygons')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['user_id'], 'polygon_pakbans_user_id_foreign')->references(['id'])->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('polygon_drivers', function (Blueprint $table) {
            $table->dropForeign('polygon_pakbans_polygon_id_foreign');
            $table->dropForeign('polygon_pakbans_user_id_foreign');
        });
    }
};
