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
        Schema::table('bazist_wallets', function (Blueprint $table) {
            $table->foreign(['operator_id'])->references(['id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bazist_wallets', function (Blueprint $table) {
            $table->dropForeign('bazist_wallets_operator_id_foreign');
        });
    }
};
