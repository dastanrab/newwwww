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
        Schema::create('events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->index('events_user_id_foreign');
            $table->string('title');
            $table->string('title_en');
            $table->string('slug');
            $table->timestamp('start_at');
            $table->timestamp('end_at');
            $table->text('text');
            $table->string('meta_keyword');
            $table->string('meta_description');
            $table->boolean('display')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
