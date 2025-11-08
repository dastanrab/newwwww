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
        Schema::table('users', function (Blueprint $table) {
            $table->foreign(['city_id'])->references(['id'])->on('cities')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['guild_id'])->references(['id'])->on('guilds')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_city_id_foreign');
            $table->dropForeign('users_guild_id_foreign');
        });
    }
};
