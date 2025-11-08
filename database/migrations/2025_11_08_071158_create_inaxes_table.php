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
        Schema::create('inaxes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->index('inaxes_user_id_foreign');
            $table->integer('amount');
            $table->string('operator')->nullable();
            $table->string('mobile');
            $table->string('method')->nullable();
            $table->string('pay_method')->nullable();
            $table->string('type')->nullable();
            $table->integer('order_id')->nullable();
            $table->integer('ref_code')->nullable();
            $table->integer('trans_id')->nullable();
            $table->text('description')->nullable();
            $table->string('status');
            $table->timestamps();
            $table->unsignedBigInteger('fava_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inaxes');
    }
};
