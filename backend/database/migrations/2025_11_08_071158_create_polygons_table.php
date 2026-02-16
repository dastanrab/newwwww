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
        Schema::create('polygons', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('city_id')->default(1);
            $table->string('region')->nullable();
            $table->text('polygon');
            $table->string('color', 64);
            $table->string('middle');
            $table->tinyInteger('has_instant')->default(1);
            $table->tinyInteger('has_legal_collect')->default(1);
            $table->tinyInteger('has_illegal_collect')->default(1);
            $table->timestamps();
            $table->tinyInteger('sort');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('polygons');
    }
};
