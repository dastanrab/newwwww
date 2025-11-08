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
        Schema::create('drivers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('city_id')->nullable()->default(1)->index('pakbans_city_id_foreign');
            $table->unsignedBigInteger('fava_id')->nullable();
            $table->unsignedBigInteger('user_id')->index('drivers_user_index_index');
            $table->unsignedBigInteger('car_id')->nullable()->index('pakbans_car_id_foreign');
            $table->unsignedBigInteger('submit_id')->index('pakbans_submit_id_foreign');
            $table->unsignedTinyInteger('status')->default(1)->index('status_index');
            $table->double('weights')->default(0)->index();
            $table->unsignedBigInteger('user_bank_code')->nullable();
            $table->unsignedBigInteger('fava_bank_code')->nullable();
            $table->timestamp('collected_at')->nullable()->index('collected_at_index');
            $table->timestamp('created_at')->nullable()->index();
            $table->timestamp('updated_at')->nullable();

            $table->index(['user_id'], 'pakbans_user_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
