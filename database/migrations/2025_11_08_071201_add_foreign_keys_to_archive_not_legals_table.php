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
        Schema::table('archive_not_legals', function (Blueprint $table) {
            $table->foreign(['city_id'])->references(['id'])->on('cities')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['receive_archive_id'])->references(['id'])->on('receive_archives')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('archive_not_legals', function (Blueprint $table) {
            $table->dropForeign('archive_not_legals_city_id_foreign');
            $table->dropForeign('archive_not_legals_receive_archive_id_foreign');
        });
    }
};
