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
        Schema::create('submits', function (Blueprint $table) {
            $table->bigIncrements('id')->index('city_id_index');
            $table->unsignedBigInteger('city_id')->nullable()->default(1)->index('submits_city_id_foreign');
            $table->unsignedBigInteger('fava_id')->nullable();
            $table->unsignedBigInteger('registrant_id')->nullable()->index();
            $table->unsignedBigInteger('user_id')->index('submits_user_id_foreign');
            $table->unsignedBigInteger('address_id')->index('submits_address_id_foreign');
            $table->bigInteger('region_id')->nullable();
            $table->timestamp('start_deadline')->index('start_deadline');
            $table->timestamp('end_deadline')->index('end_deadline');
            $table->boolean('is_instant')->default(false);
            $table->tinyInteger('type')->default(0)->comment('can be internal submit or hooshmap ,..');
            $table->double('total_amount')->default(0)->index('total_amount');
            $table->float('final_amount')->default(0)->index('final_amount');
            $table->unsignedTinyInteger('status')->default(1)->index('status_index');
            $table->json('recyclables');
            $table->integer('star')->nullable();
            $table->text('survey')->nullable();
            $table->text('comment')->nullable();
            $table->string('cancel')->nullable();
            $table->boolean('submit_phone')->default(false)->index('submits_submit_phone');
            $table->string('cashout_type')->nullable()->default('aap');
            $table->boolean('cashout_instant')->nullable()->default(false);
            $table->dateTime('canceled_at')->nullable();
            $table->unsignedBigInteger('canceller_id')->nullable()->index();
            $table->timestamp('created_at')->nullable()->index('created_at_index');
            $table->timestamp('updated_at')->nullable()->index('updated_at_index');
            $table->unsignedBigInteger('iban_id')->nullable()->index('submits_iban_id_foreign');
            $table->integer('flag')->nullable()->default(0)->index();

            $table->primary(['id']);
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submits');
    }
};
