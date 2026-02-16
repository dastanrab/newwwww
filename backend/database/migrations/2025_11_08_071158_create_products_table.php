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
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('category_id')->nullable()->index('products_category_id_foreign');
            $table->unsignedBigInteger('user_id')->index('products_user_id_foreign');
            $table->string('title')->nullable();
            $table->string('title_en');
            $table->string('slug');
            $table->text('description');
            $table->integer('price');
            $table->integer('discount')->nullable();
            $table->string('photo')->nullable();
            $table->string('meta_keyword');
            $table->string('meta_description');
            $table->boolean('display')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
