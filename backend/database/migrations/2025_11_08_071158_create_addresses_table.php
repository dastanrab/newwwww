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
        Schema::create('addresses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('city_id')->nullable()->default(1)->index('addresses_city_id_foreign');
            $table->unsignedBigInteger('user_id')->index('addresses_user_id_foreign');
            $table->string('title');
            $table->string('address');
            $table->integer('region')->nullable();
            $table->integer('district')->nullable();
            $table->double('lat')->nullable();
            $table->double('lon')->nullable();
            $table->boolean('status')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
