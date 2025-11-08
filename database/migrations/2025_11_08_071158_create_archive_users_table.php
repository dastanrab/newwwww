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
        Schema::create('archive_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('city_id')->default(1)->index('archive_users_city_id_foreign');
            $table->dateTime('date');
            $table->integer('total')->default(0);
            $table->integer('legal')->default(0);
            $table->integer('not_legal')->default(0);
            $table->integer('phone')->default(0);
            $table->integer('app')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archive_users');
    }
};
