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
        Schema::create('financial_archive', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->double('aap_amount')->nullable();
            $table->double('aap_deposite')->nullable();
            $table->double('aap_withdraw')->nullable();
            $table->double('bazist_wallet_amount')->nullable();
            $table->double('cashout')->nullable();
            $table->double('deposite')->nullable();
            $table->double('withdraw')->nullable();
            $table->double('first_submit_amount')->nullable();
            $table->double('ref_amount')->nullable();
            $table->timestamps();
            $table->double('raw_bazist_wallet_amount')->nullable();
            $table->double('inax_done')->nullable();
            $table->double('inax_cancel')->nullable();
            $table->double('cashout_deposited')->nullable();
            $table->double('cashout_depositing')->nullable();
            $table->double('cashout_refunded')->nullable();
            $table->double('cashout_waiting')->nullable();
            $table->double('asan_deposite_sharj_mobile')->nullable();
            $table->double('asan_deposite_sharj_internet')->nullable();
            $table->double('asan_deposite_asanpardakht_sharj')->nullable();
            $table->double('bazist_deposite_submit')->nullable();
            $table->double('bazist_deposite_first_submit_user')->nullable();
            $table->double('bazist_deposite_back_to_bazist_wallet')->nullable();
            $table->double('bazist_deposite_deposit')->nullable();
            $table->double('bazist_deposite_add_miss_submit')->nullable();
            $table->double('bazist_deposite_submit_user_ref')->nullable();
            $table->double('bazist_withdraw_cashout_admin')->nullable();
            $table->double('bazist_withdraw_cashout')->nullable();
            $table->double('bazist_withdraw_cashout_to_aap')->nullable();
            $table->double('bazist_withdraw_sharj_internet')->nullable();
            $table->double('bazist_withdraw_sharj_mobile')->nullable();
            $table->double('bazist_withdraw_submit_phone')->nullable();
            $table->double('bazist_withdraw_withdraw_bazist_wallet')->nullable();
            $table->double('inax_preparation')->nullable();
            $table->double('bazist_deposite_submit_phone')->nullable();
            $table->double('asan_withdraw_to_aap')->nullable();
            $table->double('bazist_total_vaariz_amount')->nullable();
            $table->double('bazist_total_bardaasht_amount')->nullable();
            $table->double('waste_amount')->nullable();
            $table->double('inax_pendingDecreaseCredit')->nullable();
            $table->double('bazist_deposite_back_mobile')->nullable();
            $table->double('bazist_deposite_back_internet')->nullable();
            $table->double('cashout_today_waiting')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_archive');
    }
};
