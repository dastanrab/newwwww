<div id="paginated-list">
    <div class="cr-card-body p-0">
        <div class="table-responsive text-center text-nowrap">
            <div wire:loading.class="cr-parent-spinner">
                {{spinner()}}
            </div>
            <table class="cr-table table" >
                <tr>
                    <th class="uk-text-center">تعداد درخواست</th>
                    <td>{{ number_format($totalSubmit) }}</td> {{-- تعداد درخواست --}}

                    <th class="uk-text-center">جمع‌آوری شده</th>
                    <td>{{ number_format($collectedSubmit) }}</td> {{-- جمع‌آوری شده --}}
                </tr>
                <tr>
                    <th class="uk-text-center">جمع‌آوری شده (صنفی)</th>
                    <td>{{ number_format($legalCollectedSubmit) }}</td> {{-- جمع‌آوری شده (صنفی) --}}

                    <th class="uk-text-center">جمع‌آوری شده (شهروندی)</th>
                    <td>{{ number_format($notLegalCollectedSubmit) }}</td> {{-- جمع‌آوری شده (شهروندی) --}}
                </tr>
                <tr>
                    <th class="uk-text-center">جمع‌آوری شده (تلفنی)</th>
                    <td>{{ number_format($phoneTotalSubmit) }}</td> {{-- جمع‌آوری شده (تلفنی) --}}

                    <th class="uk-text-center">جمع‌آوری شده (اپ)</th>
                    <td>{{ number_format($appCollectedSubmit) }}</td> {{-- جمع‌آوری شده (اپ) --}}
                </tr>
                <tr>
                    <th class="uk-text-center">لغو توسط کاربر</th>
                    <td>{{ number_format($canceledUser) }}</td> {{-- لغو توسط کاربر --}}

                    <th class="uk-text-center">لغو توسط اپراتور</th>
                    <td>{{ number_format($canceledOperator) }}</td> {{-- لغو توسط اپراتور --}}
                </tr>

                <tr>
                    <th class="uk-text-center">جمع‌آوری نشده</th>
                    <td>{{ number_format($notCollectedSubmit) }}</td> {{-- جمع‌آوری نشده --}}

                    <th class="uk-text-center">اقلام به کیلو (کل)</th>
                    <td>{{ number_format($totalWeight) }}</td> {{-- اقلام به کیلو  کل--}}
                </tr>

                <tr>
                    <th class="uk-text-center">میانگین وزن هر درخواست</th>
                    <td>{{ round($totalWeight / $totalSubmit, 2) }}</td> {{-- میانگین وزن هر درخواست --}}

                    <th class="uk-text-center">اقلام به کیلو (صنفی)</th>
                    <td>{{ number_format($totalWeightLegal) }}</td> {{-- اقلام به کیلو صنفی --}}
                </tr>

                <tr>
                    <th class="uk-text-center">درصد وزن صنفی</th>
                    <td>{{ round($totalWeightLegal / $totalWeight * 100, 2) }}</td> {{-- درصد وزن صنفی --}}

                    <th class="uk-text-center">اقلام به کیلو (شهروندی)</th>
                    <td>{{ number_format($totalWeightNotLegal) }}</td> {{-- اقلام به کیلو شهروندی --}}
                </tr>

                <tr>
                    <th class="uk-text-center">اقلام به کیلو (اپ)</th>
                    <td>{{ number_format($totalWeightApp) }}</td> {{-- اقلام به کیلو اپ --}}
                    <th class="uk-text-center">درصد وزن اپ</th>
                    <td>{{ round($totalWeightApp / $totalWeight * 100, 2) }}</td> {{-- درصد وزن اپ --}}
                </tr>

                <tr>
                    <th class="uk-text-center">اقلام به کیلو (تلفنی)</th>
                    <td>{{ number_format($totalWeightTel) }}</td> {{-- اقلام به کیلو تلفنی --}}

                    <th class="uk-text-center">ارزش کل (تومان)</th>
                    <td>{{ number_format($finalAmount) }}</td> {{--ارزش کل --}}
                </tr>

                <tr>
                    <th class="uk-text-center">سهم شهروند (تومان)</th>
                    <td>{{ number_format($totalAmount) }}</td> {{--سهم شهروند --}}

                    <th class="uk-text-center">سهم شهروند اپ (تومان)</th>
                    <td>{{ number_format($totalAmountApp) }}</td> {{--سهم شهروند اپ --}}
                </tr>

                <tr>
                    <th class="uk-text-center">سهم شهروند تلفنی (تومان)</th>
                    <td>{{ number_format($totalAmountPhone) }}</td> {{--سهم شهروند تلفنی--}}

                    <th class="uk-text-center">سهم سازمان (تومان)</th>
                    <td>{{ number_format(($finalAmount * 0.05)) }}</td> {{--سهم سازمان --}}
                </tr>

                {{--<td>{{number_format(App\Models\ReceiveArchive::where('city_id', auth()->user()->cityId())->pluck('fava_pay')->sum()/10)}}</td> // سهم سازمان--}}
            </table>
        </div>
    </div>
    <div class="cr-card-footer">
    </div>
    {{toast($errors)}}
</div>
