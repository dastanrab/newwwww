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
        Schema::create('driver_suggested_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->unsignedBigInteger('submit_id')->nullable();
            $table->integer('status')->default(0);
            $table->tinyInteger('in_regions')->default(1);
            $table->tinyInteger('is_emergency')->nullable()->default(0);
            $table->dateTime('start_at')->nullable();
            $table->timestamps();

            $table->unique(['driver_id', 'submit_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_suggested_requests');
    }
};
