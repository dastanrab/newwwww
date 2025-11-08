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
        Schema::table('drivers', function (Blueprint $table) {
            $table->foreign(['car_id'], 'pakbans_car_id_foreign')->references(['id'])->on('cars')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['city_id'], 'pakbans_city_id_foreign')->references(['id'])->on('cities')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['submit_id'], 'pakbans_submit_id_foreign')->references(['id'])->on('submits')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['user_id'], 'pakbans_user_id_foreign')->references(['id'])->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropForeign('pakbans_car_id_foreign');
            $table->dropForeign('pakbans_city_id_foreign');
            $table->dropForeign('pakbans_submit_id_foreign');
            $table->dropForeign('pakbans_user_id_foreign');
        });
    }
};
