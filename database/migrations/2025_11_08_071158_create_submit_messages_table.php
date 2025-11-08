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
        Schema::create('submit_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->index('submit_messages_user_id_foreign');
            $table->unsignedBigInteger('submit_id')->index('submit_messages_submit_id_foreign');
            $table->text('text');
            $table->integer('admin_seen')->nullable()->default(0)->index('admin_seen');
            $table->integer('driver_seen')->nullable()->default(0)->index('driver_seen');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submit_messages');
    }
};
