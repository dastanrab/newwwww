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
        Schema::table('receives', function (Blueprint $table) {
            $table->foreign(['driver_id'], 'receives_pakban_id_foreign')->references(['id'])->on('drivers')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receives', function (Blueprint $table) {
            $table->dropForeign('receives_pakban_id_foreign');
        });
    }
};
