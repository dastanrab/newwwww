<div id="paginated-list">
    <div class="cr-card-body p-0">
        <div class="table-responsive  text-center text-nowrap tableFixHead">
            <div wire:loading.class="cr-parent-spinner">
                {{spinner()}}
            </div>
            <table class="cr-table table" >
                <thead>
                <tr>
                    <th>تاریخ</th>
                    <th>موجودی کیف پول آپ (تومان)</th>
                    <th> مجموع واریز آپ (تومان)</th>
                    <th>مجموع برداشت آپ (تومان)</th>
                    <th>وایز مخزن آپ (تومان)</th>
                    <th>خرید شارژ اینترنت آپ (تومان)</th>
                    <th>خرید شارژ مکالمه آپ (تومان)</th>
                    <th>موجودی کیف پول کاربران بازیست  (تومان)</th>
                    <th>موجودی کیف پول کاربران بازیست + در انتظار پرداخت ها (تومان)</th>
                    <th> مجموع پسماند تحویلی از کاربر (تومان)</th>
                    <th> مجموع واریز کیف پول بازیست (تومان)</th>
                    <th> مجموع برداشت کیف پول بازیست (تومان)</th>
                    <th>بالانس</th>
                    <th>واریز پسماند تلفنی کیف پول بازیست (تومان)</th>
                    <th> واریز پسماند اپ پول بازیست (تومان)</th>
                    <th>واریز پول برگشتی کیف پول بازیست (تومان)</th>
                    <th>واریز پاداش اولین درخواست کیف پول بازیست (تومان)</th>
                    <th>واریز دستی ادمین کیف پول بازیست (تومان)</th>
                    <th>واریز پاداش معرف کیف پول بازیست (تومان)</th>
                    <th>واریز دزخواست ثبت نشده کیف پول بازیست (تومان)</th>
                    <th>واریز اینترنت برگشتی کیف پول بازیست (تومان)</th>
                    <th>واریز موبایل برگشتی کیف پول بازیست (تومان)</th>
                    <th>برداشت مستقیم تحویل پسماند کیف پول بازیست (تومان)</th>
                    <th>برداشت غیر مستقیم کاربر کیف پول بازیست (تومان)</th>
                    <th>برداشت غیر مستقیم کاربر(توسط ادمین) کیف پول بازیست (تومان)</th>
                    <th>برداشت برای آپ از کیف پول بازیست (تومان)</th>
                    <th>برداشت شارژ اینترنت از کیف پول بازیست (تومان)</th>
                    <th>برداشت شارژ مکالمه از کیف پول بازیست (تومان)</th>
                    <th>برداشت اختلاف (ادمین)از کیف پول بازیست (تومان)</th>
                    <th> مجموع شارژ خریداری شده آینکس (تومان)</th>
                    <th> مجموع شارژ کنسل شده آینکس (تومان)</th>
                    <th> مجموع واریز بانک پرداخت شده (تومان)</th>
                    <th> مجموع واریز بانک برگشت داده شده (تومان)</th>
                    <th> مجموع واریز بانک در انظار تایید پرداخت (تومان)</th>
                    <th> مجموع واریز بانک در انتظار پرداخت بانک (تومان)</th>
                    <th> مجموع واریز بانک در انظار تایید پرداخت امروز (تومان)</th>
                </tr>
                </thead>
                <tbody>
                @php
                    $old_wallet=0;
                    $show=true;
                @endphp
                @foreach($archive as $value)
                    @php
                        if ($old_wallet==0)
                            {
                                $balance=0;
                            }
                        else{
                            $balance=$old_wallet+$value->bazist_total_vaariz_amount-$value->bazist_total_bardaasht_amount-$value->raw_bazist_wallet_amount;
                            $balance=$balance/10;
                        }
                        $old_wallet=$value->raw_bazist_wallet_amount;
                    @endphp
                    <tr wire:key="1">
                        <td>{{ verta()->instance($value->created_at)->format('Y/m/d')}}</td>
                        <td >{{ isset($value->aap_amount)?number_format($value->aap_amount/10):0 }}</td>
                        <td style=" color: green">{{ isset($value->aap_withdraw)?number_format($value->aap_withdraw/10):0 }}</td>
                        <td style=" color: red">{{ isset($value->aap_deposite)?number_format($value->aap_deposite/10):0 }}</td>
                        <td style=" color: green">{{isset($value->asan_deposite_asanpardakht_sharj)?number_format($value->asan_deposite_asanpardakht_sharj/10):0}}</td>
                        <td style=" color: red">{{isset($value->asan_deposite_sharj_internet)?number_format($value->asan_deposite_sharj_internet/10) : 0}}</td>
                        <td style=" color: red">{{isset($value->asan_deposite_sharj_mobile)?number_format($value->asan_deposite_sharj_mobile/10):0}}</td>
                        <td>{{isset($value->raw_bazist_wallet_amount)?number_format($value->raw_bazist_wallet_amount/10):0}}</td>
                        <td>{{isset($value->bazist_wallet_amount)?number_format($value->bazist_wallet_amount/10):0}}</td>
                        <td>{{isset($value->waste_amount)?number_format($value->waste_amount/10):0}}</td>
                        <td style=" color: green">{{isset($value->bazist_total_vaariz_amount)?number_format($value->bazist_total_vaariz_amount/10):0}}</td>
                        <td style=" color: red">{{isset($value->bazist_total_bardaasht_amount)?number_format($value->bazist_total_bardaasht_amount/10):0 }}</td>
                        <td>{{number_format($balance)}}</td>
                        <td style=" color: green">{{isset($value->bazist_deposite_submit)?number_format($value->bazist_deposite_submit/10):0}}</td>
                        <td style=" color: green">{{isset($value->bazist_deposite_submit_phone)?number_format($value->bazist_deposite_submit_phone/10):0}}</td>
                        <td style=" color: green">{{isset($value->bazist_deposite_back_to_bazist_wallet)?number_format($value->bazist_deposite_back_to_bazist_wallet/10):0}}</td>
                        <td style=" color: green">{{isset($value->bazist_deposite_first_submit_user)?number_format($value->bazist_deposite_first_submit_user/10):0}}</td>
                        <td style=" color: green">{{isset($value->bazist_deposite_deposit)?number_format($value->bazist_deposite_deposit/10):0}}</td>
                        <td style=" color: green">{{isset($value->bazist_deposite_submit_user_ref)?number_format($value->bazist_deposite_submit_user_ref/10):0}}</td>
                        <td style=" color: green">{{isset($value->bazist_deposite_add_miss_submit)?number_format($value->bazist_deposite_add_miss_submit/10):0}}</td>
                        <td style=" color: green">{{isset($value->bazist_deposite_back_internet)?number_format($value->bazist_deposite_back_internet/10):0}}</td>
                        <td style=" color: green">{{isset($value->bazist_deposite_back_mobile)?number_format($value->bazist_deposite_back_mobile/10):0}}</td>
                        <td style=" color: red">{{isset($value->bazist_withdraw_submit_phone)?number_format($value->bazist_withdraw_submit_phone/10):0}}</td>
                        <td style=" color: red">{{isset($value->bazist_withdraw_cashout)?number_format($value->bazist_withdraw_cashout/10):0}}</td>
                        <td style=" color: red">{{isset($value->bazist_withdraw_cashout_admin)?number_format($value->bazist_withdraw_cashout_admin/10):0}}</td>
                        <td style=" color: red">{{isset($value->bazist_withdraw_cashout_to_aap)?number_format($value->bazist_withdraw_cashout_to_aap/10):0}}</td>
                        <td style=" color: red">{{isset($value->bazist_withdraw_sharj_internet)?number_format($value->bazist_withdraw_sharj_internet/10):0}}</td>
                        <td style=" color: red">{{isset($value->bazist_withdraw_sharj_mobile)?number_format($value->bazist_withdraw_sharj_mobile/10):0}}</td>
                        <td style=" color: red">{{isset($value->bazist_withdraw_withdraw_bazist_wallet)?number_format($value->bazist_withdraw_withdraw_bazist_wallet/10):0}}</td>
                        <td >{{isset($value->inax_done)?number_format($value->inax_done/10):0}}</td>
                        <td >{{isset($value->inax_cancel)?number_format($value->inax_cancel/10):0}}</td>
                        <td >{{isset($value->cashout_deposited)?number_format($value->cashout_deposited/10):0}}</td>
                        <td >{{isset($value->cashout_refunded)?number_format($value->cashout_refunded/10):0}}</td>
                        <td >{{isset($value->cashout_waiting)?number_format($value->cashout_waiting/10):0}}</td>
                        <td >{{isset($value->cashout_depositing)?number_format($value->cashout_depositing/10):0}}</td>
                        <td >{{isset($value->cashout_today_waiting)?number_format($value->cashout_today_waiting/10):0}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="cr-card-footer">
    </div>
    @script
    <script>

    </script>
    @endscript
</div>
