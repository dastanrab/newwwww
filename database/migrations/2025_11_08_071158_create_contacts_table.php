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
        Schema::create('contacts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->default(0);
            $table->boolean('reply')->default(false);
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('subject')->nullable();
            $table->text('message');
            $table->string('ip', 45)->nullable();
            $table->timestamp('admin_seen_at')->nullable();
            $table->timestamp('user_seen_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
