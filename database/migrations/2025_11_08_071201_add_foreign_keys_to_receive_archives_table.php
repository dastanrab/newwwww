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
        Schema::table('receive_archives', function (Blueprint $table) {
            $table->foreign(['city_id'])->references(['id'])->on('cities')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receive_archives', function (Blueprint $table) {
            $table->dropForeign('receive_archives_city_id_foreign');
        });
    }
};
