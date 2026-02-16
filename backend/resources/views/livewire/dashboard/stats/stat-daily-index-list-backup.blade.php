@php use App\Models\Driver;use App\Models\Receive;use App\Models\Submit;use Hekmatinasser\Verta\Verta; @endphp
<div id="paginated-list">
    <div class="cr-card-body p-0">
        <div class="table-responsive text-center text-nowrap">
            <div wire:loading.class="cr-parent-spinner">
                {{spinner()}}
            </div>
            <table class="cr-table table">
                <thead>
                <tr>
                    <th class="uk-text-center">تاریخ</th>
                    <th class="uk-text-center">تعداد درخواست</th>
                    <th class="uk-text-center">جمع‌آوری شده</th>
                    {{-- <th class="uk-text-center">درخواست اولی</th> --}}
                    <th class="uk-text-center">جمع‌آوری نشده</th>
                    <th class="uk-text-center">لغو شده</th>
                    <th class="uk-text-center">حذف شده</th>
                    <th class="uk-text-center">اقلام به کیلو (کل)</th>
                    @if (auth()->user()->getRole('name') !== 'supervisor')
                        <th class="uk-text-center">میانگین وزن هر درخواست</th>
                        <th class="uk-text-center">هزینه تمام شده (تومان/کیلو)</th>
                        <th class="uk-text-center">اقلام به کیلو (صنفی)</th>
                        <th class="uk-text-center">درصد وزن (صنفی)</th>
                        <th class="uk-text-center">اقلام به کیلو (شهروندی)</th>
                        <th class="uk-text-center">اقلام به کیلو (اپلکیشن)</th>
                        <th class="uk-text-center">درصد وزن (اپلکیشن)</th>
                        <th class="uk-text-center">اقلام به کیلو (تلفنی)</th>
                    @endif
                    <th class="uk-text-center">ارزش کل (تومان)</th>
                    <th class="uk-text-center">سهم شهروند (تومان)</th>
                    @if (auth()->user()->getRole('name') !== 'supervisor')
                        <th class="uk-text-center">سهم شهروند اپ (تومان)</th>
                        <th class="uk-text-center">سهم شهروند تلفنی (تومان)</th>
                    @endif
                    <th class="uk-text-center">سهم سازمان (تومان)</th>
                    <th class="uk-text-center">مبلغ پرداختی به سازمان (تومان)</th>
                </tr>
                </thead>
                <tbody>
                <tr class="text-bold">
                    <td>جمع کل</td>
                    @php

                        @endphp
                    <td>{{ number_format($total_count) }}</td>
                    <td>{{ number_format($total_done_count) }}</td>
                    <td>{{ number_format($total_not_collected_count) }}</td>
                    <td>{{ number_format($total_cancel_count) }}</td>
                    <td>{{ number_format($total_delete_count) }}</td>
                    <td>{{ number_format($total_weight) }}</td>
                    @if (auth()->user()->getRole('name') !== 'supervisor')
                        <td>{{ round($total_weight / $total_count, 2) }}</td>
                        <td>-</td>
                        <td>{{ number_format($total_weight_legal) }}</td>
                        <td>{{ round($total_weight_legal / $total_weight * 100, 2) }}</td>
                        <td>{{ number_format($total_weight_not_legal) }}</td>
                        <td>{{ number_format($total_weight_app) }}</td>
                        <td>{{ round($total_weight_app / $total_weight * 100, 2) }}</td>
                        <td>{{ number_format($total_weight_tel) }}</td>
                    @endif
                    <td>{{ number_format($final_amount) }}</td>
                    <td>{{ number_format($total_amount_app+$total_amount_tel) }}</td>
                    @if (auth()->user()->getRole('name') !== 'supervisor')
                        <td>{{ number_format($total_amount_app) }}</td>
                        <td>{{ number_format($total_amount_tel) }}</td>
                    @endif
                    <td>{{ number_format($fava_share) }}</td>
                    <td>{{ number_format($fava_paid) }}</td>
                </tr>
                <tr class="text-bold">
                    @php
                        $user_pay_app = App\Models\ArchiveApp::where('city_id', auth()->user()->cityId())->whereBetween('date', [$start_date, $end_date])->pluck('user_pay')->sum();
                        $user_pay = $submits->pluck('user_pay')->sum();
                    @endphp
                    <td>جمع ماهانه</td>
                    <td>{{ number_format($total_month_count) }}</td>
                    <td>{{ number_format($total_month_done_count) }}</td>
                    <td>{{ number_format($total_month_not_collected_count)}}</td>
                    <td>{{ number_format($total_month_cancel_count) }}</td>
                    <td>{{ number_format($total_month_delete_count) }}</td>
                    <td>{{ $month_weight }}</td>
                    @if (auth()->user()->getRole('name') !== 'supervisor')
                        <td>{{ $month_weight ? round($month_weight / $total_month_count, 2) : 0 }}</td>
                        <td>-</td>
                        <td>{{ $month_weight_legal }}</td>
                        <td>{{ $month_weight ? round($month_weight_legal / $month_weight * 100) : $month_weight_legal = 0 }}</td>
                        <td>{{ $month_weight_not_legal }}</td>
                        <td>{{ $weight_month_app }}</td>
                        <td>{{ $month_weight ? round($weight_month_app / $month_weight * 100) : $weight_month_app = 0 }}</td>
                        <td>{{ number_format($month_weight - $weight_month_app) }}</td>
                    @endif
                    <td>{{ number_format($final_month_amount) }}</td>
                    <td>{{ number_format($total_month_amount_app+$total_month_amount_tel) }}</td>
                    @if (auth()->user()->getRole('name') !== 'supervisor')
                        <td>{{ number_format($total_month_amount_app) }}</td>
                        <td>{{ number_format(($total_month_amount_tel)) }}</td>
                    @endif
                    <td>0</td>
                    <td>{{ number_format($fava_month_pay) }}</td>
                </tr>
                @php
                    $submits = [];
                    $i = 0;
                @endphp
                @foreach($submits as $submit)
                    <tr wire:key="{{$submit->id}}">
                        <td>
                            {{ $dateRange }}
                        </td>
                        <td>{{ $all_total["key_{$i}"] }}</td> {{-- تعداد درخواست --}}
                        <td>{{ $all_total["key_{$i}"] }}</td> {{-- جمع آوری شده --}}
                        <td>{{ $all_total_not_collected["key_{$i}"] }}</td> {{-- جمع آوری نشده --}}
                        <td>{{ $all_total_cancel["key_{$i}"] }}</td> {{-- لغو شده --}}
                        <td>{{ $all_total_delete["key_{$i}"] }}</td> {{-- حذف شده --}}
                        <td>{{ $all_total_weight["key_{$i}"] }}</td> {{-- اقلام به کیلو (کل) --}}
                        @if (auth()->user()->getRole('name') !== 'supervisor')
                            <td>{{ $all_total["key_{$i}"] ? round($all_total_weight["key_{$i}"] / $all_total["key_{$i}"]) : 0 }}</td> {{-- میانگین وزن هر درخواست --}}
                            @php
                                $rollcalls = App\Models\Rollcall::where('end_at', '!=', null)->whereDate('start_at',Verta::parse($dateRange)->toCarbon())->get();
                                $sum_rollcall = 0;
                                foreach ($rollcalls as $rollcall){
                                    $sum_rollcall += Carbon\Carbon::parse($rollcall->start_at)->diffInMinutes($rollcall->end_at);
                                }
                            @endphp
                            <td class="number">{{ $all_total_weight["key_{$i}"] ? round(($all_total_user_pay["key_{$i}"] + ($sum_rollcall / 60) * 700000 + $all_total_user_pay["key_{$i}"] * 0.05 + $all_total_user_pay["key_{$i}"] * 0.06) / $all_total_weight["key_{$i}"]) : 0 }}</td> {{-- هزینه تمام شده (تومان/کیلو) --}}
                            <td>{{ $submit->archiveLegal ? $legal_weight = $submit->archiveLegal->weight : $legal_weight = 0 }}</td> {{-- اقلام به کیلو (صنفی) --}}
                            <td>{{ $submit->weight ? round($legal_weight / $submit->weight * 100) : $legal_weight = 0 }}</td> {{-- درصد وزن (صنفی) --}}
                            <td>{{ $submit->weight - $legal_weight }}</td> {{-- اقلام به کیلو (شهروندی) --}}
                            <td>{{ $submit->archiveApp ? $app_weight = $submit->archiveApp->weight : $app_weight = 0 }}</td>  {{-- اقلام به کیلو (اپلکیشن) --}}
                            <td>{{ $submit->weight ? round($app_weight / $submit->weight * 100) : $app_weight = 0 }}</td>  {{-- درصد وزن (اپلکیشن) --}}
                            <td>{{ $submit->weight - $app_weight }}</td> {{-- اقلام به کیلو (تلفنی) --}}
                        @endif
                        <td>{{ number_format($submit->value/10) }}</td> {{-- ارزش کل (تومان) --}}
                        <td>{{ number_format(($submit->user_pay)/10) }}</td> {{-- سهم شهروند (تومان) --}}
                        @if (auth()->user()->getRole('name') !== 'supervisor')
                            @php
                                $user_pay = $submit->archiveApp ?  $submit->archiveApp->user_pay : 0;
                            @endphp
                            <td>{{ number_format($user_pay/10) }}</td> {{-- سهم شهروند اپ (تومان) --}}
                            <td>{{ number_format(($submit->user_pay - $user_pay)/10) }}</td> {{-- سهم شهروند تلفنی (تومان) --}}
                        @endif
                        @php
                            $submit->archiveLegal ? $fava_pay_share1 = $submit->archiveLegal->fava_pay_share : $fava_pay_share1 = 0;
                            $submit->archiveNotLegal ? $fava_pay_share2 = $submit->archiveNotLegal->fava_pay_share : $fava_pay_share2 = 0;
                        @endphp
                        <td>{{ number_format(($fava_pay_share1 + $fava_pay_share2)/10) }}</td> {{-- سهم سازمان (تومان) --}}
                        <td>{{ number_format($submit->fava_pay/10) }}</td> {{-- مبلغ پرداختی به سازمان (تومان) --}}
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
        $(document).ready(function () {

        })
    </script>
    @endscript
    {{toast($errors)}}
</div>
