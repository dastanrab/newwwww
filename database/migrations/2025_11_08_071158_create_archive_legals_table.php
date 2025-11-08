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
        Schema::create('archive_legals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('city_id')->nullable()->default(1)->index('archive_legals_city_id_foreign');
            $table->unsignedBigInteger('receive_archive_id')->index('archive_legals_receive_archive_id_foreign');
            $table->dateTime('date');
            $table->integer('type');
            $table->integer('submit_count')->nullable();
            $table->integer('submit_done')->nullable();
            $table->integer('submit_first')->nullable();
            $table->integer('submit_cancel')->nullable();
            $table->integer('submit_delete')->nullable();
            $table->double('weight')->nullable();
            $table->double('value')->nullable();
            $table->double('user_pay')->nullable();
            $table->double('fava_pay')->nullable();
            $table->double('fava_pay_share')->nullable();
            $table->double('recyclable_1')->default(0);
            $table->double('recyclable_2')->default(0);
            $table->double('recyclable_3')->default(0);
            $table->double('recyclable_4')->default(0);
            $table->double('recyclable_5')->default(0);
            $table->double('recyclable_6')->default(0);
            $table->double('recyclable_7')->default(0);
            $table->double('recyclable_8')->default(0);
            $table->double('recyclable_9')->default(0);
            $table->double('recyclable_10')->default(0);
            $table->double('recyclable_11')->default(0);
            $table->double('recyclable_12')->default(0);
            $table->double('recyclable_13')->default(0);
            $table->double('recyclable_14')->default(0);
            $table->double('recyclable_15')->default(0);
            $table->double('recyclable_16')->default(0);
            $table->double('recyclable_17')->default(0);
            $table->double('recyclable_18')->default(0);
            $table->double('recyclable_19')->default(0);
            $table->double('recyclable_20')->default(0);
            $table->double('recyclable_21')->default(0);
            $table->double('recyclable_22')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archive_legals');
    }
};
