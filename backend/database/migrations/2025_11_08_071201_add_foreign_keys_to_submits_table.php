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
        Schema::table('submits', function (Blueprint $table) {
            $table->foreign(['address_id'])->references(['id'])->on('addresses')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['canceller_id'])->references(['id'])->on('users')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['city_id'])->references(['id'])->on('cities')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['iban_id'])->references(['id'])->on('ibans')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['registrant_id'])->references(['id'])->on('users')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submits', function (Blueprint $table) {
            $table->dropForeign('submits_address_id_foreign');
            $table->dropForeign('submits_canceller_id_foreign');
            $table->dropForeign('submits_city_id_foreign');
            $table->dropForeign('submits_iban_id_foreign');
            $table->dropForeign('submits_registrant_id_foreign');
            $table->dropForeign('submits_user_id_foreign');
        });
    }
};
