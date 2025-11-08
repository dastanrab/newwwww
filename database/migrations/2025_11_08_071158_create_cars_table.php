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
        Schema::create('cars', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('city_id')->nullable()->default(1)->index('cars_city_id_foreign');
            $table->unsignedBigInteger('fava_id')->nullable();
            $table->unsignedBigInteger('user_id')->index('cars_user_id_foreign');
            $table->string('plaque');
            $table->integer('plaque_1')->nullable();
            $table->string('plaque_2')->nullable();
            $table->integer('plaque_3')->nullable();
            $table->integer('plaque_4')->nullable();
            $table->string('type');
            $table->integer('type_id');
            $table->string('imei')->nullable();
            $table->string('imei_hex')->nullable();
            $table->string('simcard')->nullable();
            $table->string('ip', 45)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->integer('rollcall_status')->default(0)->index();
            $table->timestamp('created_at')->nullable()->index();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
