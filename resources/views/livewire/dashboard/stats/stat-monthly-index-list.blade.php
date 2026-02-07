<div id="paginated-list">
    <div class="cr-card-body p-0">
        <div class="table-responsive text-center text-nowrap">
            <div wire:loading.class="cr-parent-spinner">
                {{spinner()}}
            </div>
            <table class="cr-table table" >
                <tr>
                    <th class="uk-text-center">تعداد درخواست</th>
                    <td>{{ number_format($monthTotalSubmit) }}</td> {{-- تعداد درخواست --}}

                    <th class="uk-text-center">جمع‌آوری شده</th>
                    <td>{{ number_format($monthCollectedSubmit) }}</td> {{-- جمع‌آوری شده --}}
                </tr>
                <tr>
                    <th class="uk-text-center">جمع‌آوری شده صنفی</th>
                    <td>{{ number_format($monthLegalCollectedSubmit) }}</td> {{-- تعداد درخواست صنفی--}}

                    <th class="uk-text-center">جمع‌آوری شده غیرصنفی</th>
                    <td>{{ number_format($monthNotLegalCollectedSubmit) }}</td> {{-- جمع‌آوری شده غیر صنفی--}}
                </tr>
                <tr>
                    <th class="uk-text-center">لغو توسط کاربر</th>
                    <td>{{ number_format($monthCanceledUser) }}</td> {{-- لغو توسط کاربر --}}

                    <th class="uk-text-center">لغو توسط اپراتور</th>
                    <td>{{ number_format($monthCanceledOperator) }}</td> {{-- لغو توسط اپراتور --}}
                </tr>

                <tr>
                    <th class="uk-text-center">جمع‌آوری نشده</th>
                    <td>{{ number_format($monthNotCollectedSubmit) }}</td> {{-- جمع‌آوری نشده --}}

                    <th class="uk-text-center">اقلام به کیلو (کل)</th>
                    <td>{{ number_format($monthTotalWeight) }}</td> {{-- اقلام به کیلو  کل--}}
                </tr>

                <tr>
                    <th class="uk-text-center">میانگین وزن هر درخواست</th>
                    <td>{{ round($monthTotalWeight / $monthTotalSubmit, 2) }}</td> {{-- میانگین وزن هر درخواست --}}

                    <th class="uk-text-center">اقلام به کیلو (صنفی)</th>
                    <td>{{ number_format($monthTotalWeightLegal) }}</td> {{-- اقلام به کیلو صنفی --}}
                </tr>

                <tr>
                    <th class="uk-text-center">درصد وزن صنفی</th>
                    <td>{{ round($monthTotalWeightLegal / $monthTotalWeight * 100, 2) }}</td> {{-- درصد وزن صنفی --}}

                    <th class="uk-text-center">اقلام به کیلو (شهروندی)</th>
                    <td>{{ number_format($monthTotalWeightNotLegal) }}</td> {{-- اقلام به کیلو شهروندی --}}
                </tr>

                <tr>
                    <th class="uk-text-center">اقلام به کیلو (اپ)</th>
                    <td>{{ number_format($monthTotalWeightApp) }}</td> {{-- اقلام به کیلو اپ --}}
                    <th class="uk-text-center">درصد وزن اپ</th>
                    <td>{{ round($monthTotalWeightApp / $monthTotalWeight * 100, 2) }}</td> {{-- درصد وزن اپ --}}
                </tr>

                <tr>
                    <th class="uk-text-center">اقلام به کیلو (تلفنی)</th>
                    <td>{{ number_format($monthTotalWeightTel) }}</td> {{-- اقلام به کیلو تلفنی --}}

                    <th class="uk-text-center">ارزش کل (تومان)</th>
                    <td>{{ number_format($monthFinalAmount) }}</td> {{--ارزش کل --}}
                </tr>

                <tr>
                    <th class="uk-text-center">سهم شهروند (تومان)</th>
                    <td>{{ number_format($monthTotalAmount) }}</td> {{--سهم شهروند --}}

                    <th class="uk-text-center">سهم شهروند اپ (تومان)</th>
                    <td>{{ number_format($monthTotalAmountApp) }}</td> {{--سهم شهروند اپ --}}
                </tr>

                <tr>
                    <th class="uk-text-center">سهم شهروند تلفنی (تومان)</th>
                    <td>{{ number_format($monthTotalAmountPhone) }}</td> {{--سهم شهروند تلفنی--}}

                    <th class="uk-text-center">سهم سازمان (تومان)</th>
                    <td>{{ number_format(($monthFinalAmount * 0.05)) }}</td> {{--سهم سازمان --}}
                </tr>
                <tr>
                    <th class="uk-text-center">سهم شهروند واریزی به کارت مستقیم(تومان)</th>
                    <td>{{ number_format($card_vaariz_sum) }}</td> {{--سهم شهروند کارت--}}
                    <th class="uk-text-center">سهم شهروند واریزی به آپ مستقیم(تومان)</th>
                    <td>{{ number_format(($asan_pardakht_vaariz_sum)) }}</td> {{--سهم شهروند آپ --}}
                </tr>
                <tr>
                    <th class="uk-text-center">سهم شهروند واریزی به کارت غیر مستقیم(تومان)</th>
                    <td>{{ number_format($user_cashout_sum) }}</td> {{--سهم شهروند کارت--}}
                    <th class="uk-text-center">سهم شهروند واریزی به آپ  غیر مستقیم(تومان)</th>
                    <td>{{ number_format(($user_aap_cashout_sum)) }}</td> {{--سهم شهروند آپ --}}
                </tr>
                <tr>
                    <th class="uk-text-center">سهم شهروند واریزی به کارت توسط ادمین(تومان)</th>
                    <td>{{ number_format($admin_cashout_sum) }}</td> {{--سهم شهروند کارت--}}
                    <th class="uk-text-center">سهم شهروند خرید شارژ(تومان)</th>
                    <td>{{ number_format(($sharj_vaariz_sum)) }}</td> {{--سهم شهروند آپ --}}
                </tr>
                <tr>
                    <th class="uk-text-center">سهم شهروند واریزی بانکی  مجموع (تومان)</th>
                    <td>{{ number_format(($total_sum)) }}</td> {{--سهم شهروند آپ --}}
                    <th class="uk-text-center">سهم شهروند برگشتی از کیف پول (تومان)</th>
                    <td>{{ number_format(($return_from_user_wallet_sum)) }}</td> {{--سهم شهروند آپ --}}
                </tr>

                {{--<td>{{number_format(App\Models\ReceiveArchive::where('city_id', auth()->user()->cityId())->pluck('fava_pay')->sum()/10)}}</td> // سهم سازمان--}}
            </table>
        </div>
    </div>
    <div class="cr-card-footer">
    </div>
    {{toast($errors)}}
</div>
