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
        Schema::create('percentages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('recyclable_id')->index('percentages_recyclable_id_foreign');
            $table->boolean('is_legal')->default(false);
            $table->integer('weight');
            $table->double('percent');
            $table->integer('price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('percentages');
    }
};
