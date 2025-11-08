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
        Schema::table('category_club', function (Blueprint $table) {
            $table->foreign(['category_id'])->references(['id'])->on('club_categories')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['club_id'])->references(['id'])->on('clubs')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('category_club', function (Blueprint $table) {
            $table->dropForeign('category_club_category_id_foreign');
            $table->dropForeign('category_club_club_id_foreign');
        });
    }
};
