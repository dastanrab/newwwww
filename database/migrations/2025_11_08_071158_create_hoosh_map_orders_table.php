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
        Schema::create('hoosh_map_orders', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('hoosh_order_id')->nullable()->unique('hoosh_order_id');
            $table->integer('submit_id')->nullable()->unique('submit_id');
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hoosh_map_orders');
    }
};
