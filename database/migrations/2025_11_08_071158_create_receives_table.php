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
        Schema::create('receives', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('driver_id')->index();
            $table->string('title');
            $table->unsignedBigInteger('fava_id')->nullable()->index();
            $table->integer('price');
            $table->double('fava_price')->nullable()->default(0);
            $table->double('weight')->index();
            $table->timestamps();

            $table->index(['driver_id'], 'receives_pakban_id_foreign');
            $table->index(['weight'], 'weight_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receives');
    }
};
