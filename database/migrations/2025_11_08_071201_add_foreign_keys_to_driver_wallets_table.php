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
        Schema::table('driver_wallets', function (Blueprint $table) {
            $table->foreign(['user_id'], 'pakban_wallets_user_id_foreign')->references(['id'])->on('users')->onUpdate('cascade')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('driver_wallets', function (Blueprint $table) {
            $table->dropForeign('pakban_wallets_user_id_foreign');
        });
    }
};
