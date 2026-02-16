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
        Schema::table('submit_phoneuser', function (Blueprint $table) {
            $table->foreign(['phoneuser_id'])->references(['id'])->on('phoneusers')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['submit_id'])->references(['id'])->on('submits')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submit_phoneuser', function (Blueprint $table) {
            $table->dropForeign('submit_phoneuser_phoneuser_id_foreign');
            $table->dropForeign('submit_phoneuser_submit_id_foreign');
            $table->dropForeign('submit_phoneuser_user_id_foreign');
        });
    }
};
