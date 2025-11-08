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
        Schema::create('rollcalls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->index('rollcalls_user_id_foreign');
            $table->decimal('start_lat', 10, 8);
            $table->decimal('start_lon', 11, 8);
            $table->decimal('end_lat', 10, 8)->nullable();
            $table->decimal('end_lon', 11, 8)->nullable();
            $table->timestamp('start_at')->index();
            $table->timestamp('end_at')->nullable();
            $table->timestamps();

            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rollcalls');
    }
};
