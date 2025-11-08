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
        Schema::create('polygon_drivers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->index('polygon_pakbans_user_id_foreign');
            $table->unsignedBigInteger('polygon_id')->index('polygon_pakbans_polygon_id_foreign');
            $table->timestamps();

            $table->index(['polygon_id'], 'polygon_pakbans_polygon_id_index');
            $table->index(['user_id'], 'polygon_pakbans_user_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('polygon_drivers');
    }
};
