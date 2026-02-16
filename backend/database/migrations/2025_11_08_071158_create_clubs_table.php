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
        Schema::create('clubs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('title')->index();
            $table->string('sub_title')->nullable();
            $table->text('content')->nullable();
            $table->text('image')->nullable();
            $table->text('brand_icon')->nullable();
            $table->integer('score')->index();
            $table->string('status', 50)->index();
            $table->tinyInteger('has_site')->default(0);
            $table->integer('discount_type')->nullable()->default(1);
            $table->integer('discount_value')->nullable()->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clubs');
    }
};
