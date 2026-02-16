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
        Schema::table('percentages', function (Blueprint $table) {
            $table->foreign(['recyclable_id'])->references(['id'])->on('recyclables')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('percentages', function (Blueprint $table) {
            $table->dropForeign('percentages_recyclable_id_foreign');
        });
    }
};
