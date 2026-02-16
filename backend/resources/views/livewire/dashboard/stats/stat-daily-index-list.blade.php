@php
    $gateFull = Gate::allows('stat_daily_index_full',App\Livewire\Dashboard\Stats\StatSubmitIndex::class);
@endphp
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
                    @if($gateFull)
                        <th class="uk-text-center">تعداد درخواست</th>
                    @endif
                    <th class="uk-text-center">جمع‌آوری شده</th>
                    <th class="uk-text-center">جمع‌آوری (صنفی)</th>
                    <th class="uk-text-center">جمع‌آوری (شهروندی)</th>
                    @if($gateFull)
                        <th class="uk-text-center">جمع‌آوری نشده</th>
                        <th class="uk-text-center">لغو توسط اپراتور</th>
                        <th class="uk-text-center">لغو توسط کاربر</th>
                    @endif
                    <th class="uk-text-center">اقلام به کیلو (کل)</th>
                    @if (auth()->user()->getRole('name') !== 'supervisor')
                        @if($gateFull)
                            <th class="uk-text-center">میانگین وزن هر درخواست</th>
                            <th class="uk-text-center">هزینه تمام شده (تومان/کیلو)</th>
                        @endif
                        <th class="uk-text-center">اقلام به کیلو (صنفی)</th>
                        <th class="uk-text-center">درصد وزن صنفی</th>
                        <th class="uk-text-center">اقلام به کیلو (شهروندی)</th>
                        @if($gateFull)
                            <th class="uk-text-center">اقلام به کیلو (اپ)</th>
                            <th class="uk-text-center">درصد وزن اپ</th>
                            <th class="uk-text-center">اقلام به کیلو (تلفنی)</th>
                        @endif
                    @endif
                    @if($gateFull or auth()->user()->getRole('name') == 'financial_manager')
                        <th class="uk-text-center">ارزش کل</th>
                    @endif
                    @if($gateFull)
{{--                        <th class="uk-text-center">ارزش کل</th>--}}
                        <th class="uk-text-center">سهم شهروند</th>
                        @if (auth()->user()->getRole('name') !== 'supervisor')
                            <th class="uk-text-center">سهم شهروند اپ</th>
                            <th class="uk-text-center">سهم شهروند تلفنی</th>
                        @endif
                        <th class="uk-text-center">سهم سازمان</th>
                        {{-- <th class="uk-text-center">مبلغ پرداختی به سازمان</th>--}}
                    @endif
                </tr>
                </thead>
                <tbody>
                @if($gateFull)
                    @foreach($future as $date => $item)
                        <tr>
                            <td>{{$date}}</td>
                            <td>{{$item[0]->total}}</td>
                            <td>{{$item[0]->collected}}</td>
                            <td>{{$item[0]->legalCollected}}</td>
                            <td>{{$item[0]->notLegalCollected}}</td>
                            <td>{{$item[0]->total - $item[0]->collected - $item[0]->canceledOperator -$item[0]->canceledUser}}</td>
                            <td>{{$item[0]->canceledOperator}}</td>
                            <td>{{$item[0]->canceledUser}}</td>
                            @for($i=0;$i<14;$i++)
                                <td>-</td>
                            @endfor
                        </tr>
                    @endforeach
                @endif
                @isset($today->date)
                    <tr>
                        <td>{{$today->date}}</td> {{-- تاریخ --}}
                        @if($gateFull)
                            <td>{{number_format($today->totalSubmit)}}</td> {{-- تعداد درخواست --}}
                        @endif
                        <td>{{number_format($today->collectedSubmit)}}</td> {{-- جمع آوری شده --}}
                        <td>{{number_format($today->legalCollectedSubmit)}}</td> {{-- جمع آوری (صنفی) --}}
                        <td>{{number_format($today->notLegalCollectedSubmit)}}</td> {{-- جمع آوری (شهروندی) --}}
                        @if($gateFull)
                            <td>{{number_format($today->notCollectedSubmit)}}</td> {{-- جمع آوری نشده --}}
                            <td>{{number_format($today->canceledOperator)}}</td> {{-- لغو شده اپراتور --}}
                            <td>{{number_format($today->canceledUser)}}</td> {{-- لغو شده کاربر --}}
                        @endif
                        <td>{{$today->totalWeight}}</td> {{-- اقلام به کیلو --}}
                        @if (auth()->user()->getRole('name') !== 'supervisor')
                            @if($gateFull)
                                <td>{{ $today->totalSubmit ? round($today->totalWeight / $today->totalSubmit) : 0 }}</td> {{-- میانگین وزن هر درخواست --}}
                                @php
                                    $rollcalls = App\Models\Rollcall::where('end_at', '!=', null)->whereDate('start_at',verta()->parse($today->date)->toCarbon())->get();
                                    $sum_rollcall = 0;
                                    foreach ($rollcalls as $rollcall){
                                        $sum_rollcall += Carbon\Carbon::parse($rollcall->start_at)->diffInMinutes($rollcall->end_at);
                                    }
                                @endphp
                                <td class="number">{{ $today->totalWeight ? number_format(round(($today->totalAmount + ($sum_rollcall / 60) * 85000 + $today->finalAmount * 0.05 + $today->finalAmount * 0.06) / $today->totalWeight)) : 0 }}</td> {{-- هزینه تمام شده --}}
                            @endif
                            <td>{{ $today->totalWeightLegal }}</td> {{-- اقلام به کیلو (صنفی) --}}
                            <td>{{ $today->totalWeight ? number_format(round($today->totalWeightLegal / $today->totalWeight  * 100)) : 0 }}</td> {{-- درصد وزن صنفی --}}
                            <td>{{ $today->totalWeightNotLegal }}</td> {{-- اقلام به کیلو (شهروندی) --}}
                            @if($gateFull)
                                <td>{{ $today->totalWeightApp }}</td> {{-- اقلام به کیلو (اپ) --}}
                                <td>{{ $today->totalWeight ? round($today->totalWeightApp / $today->totalWeight * 100) : 0 }}</td> {{-- درصد وزن اپ --}}
                                <td>{{ $today->totalWeight - $today->totalWeightApp }}</td> {{-- اقلام به کیلو تلفنی --}}
                            @endif
                        @endif
                        @if($gateFull or auth()->user()->getRole('name') == 'financial_manager')
                            <td>{{ number_format($today->finalAmount) }}</td> {{-- ارزش کل --}}
                        @endif
                        @if($gateFull)
{{--                            <td>{{ number_format($today->finalAmount) }}</td> --}}{{-- ارزش کل --}}
                            <td>{{ number_format($today->totalAmount) }}</td> {{-- سهم شهروند --}}
                            @if (auth()->user()->getRole('name') !== 'supervisor')
                                <td>{{ number_format($today->totalAmountApp) }}</td> {{-- سهم شهروند اپ --}}
                                <td>{{ number_format($today->totalAmountPhone) }}</td> {{-- سهم شهروند تلفنی --}}
                            @endif
                            <td>{{ number_format($today->finalAmount * 0.05) }}</td> {{-- سهم سازمان --}}
                            {{--<td>{{ number_format($submit->fava_pay/10) }}</td>--}} {{-- مبلغ پرداختی به سازمان --}}
                        @endif
                    </tr>
                @endisset
                @foreach($submits as $submit)
                    <tr wire:key="{{$submit->id}}">
                        <td>{{ \Verta::instance($submit->date)->format('Y/m/d') }}</td> {{-- تاریخ --}}
                        @if($gateFull)
                            <td>{{ $submit->submit_count }}</td> {{-- تعداد درخواست --}}
                        @endif
                        <td>{{ $submit->submit_done }}</td> {{-- جمع آوری شده --}}
                        <td>{{ $submit->legal_submit_count }}</td> {{-- جمع آوری (صنفی) --}}
                        <td>{{ $submit->illegal_submit_count }}</td> {{-- جمع آوری (شهروندی) --}}
                        @if($gateFull)
                            <td>{{ $submit->submit_count -  $submit->submit_done - $submit->submit_cancel - $submit->submit_delete }}</td> {{-- جمع آوری نشده --}}
                            <td>{{ $submit->submit_cancel }}</td> {{-- لغو شده --}}
                            <td>{{ $submit->submit_delete }}</td> {{-- حذف شده --}}
                        @endif
                        <td>{{ $submit->weight }}</td> {{-- اقلام به کیلو --}}
                        @if (auth()->user()->getRole('name') !== 'supervisor')
                            @if($gateFull)
                                <td>{{ $submit->submit_count ? round($submit->weight / $submit->submit_count) : 0 }}</td> {{-- میانگین وزن هر درخواست --}}
                                @php
                                    $rollcalls = App\Models\Rollcall::where('end_at', '!=', null)->whereDate('start_at',$submit->date)->get();
                                    $sum_rollcall = 0;
                                    foreach ($rollcalls as $rollcall){
                                        $sum_rollcall += Carbon\Carbon::parse($rollcall->start_at)->diffInMinutes($rollcall->end_at);
                                    }
                                @endphp
                                <td class="number">{{ $submit->weight ? number_format(round((($submit->user_pay/10) + ($sum_rollcall / 60) * 85000 + ($submit->value/10) * 0.05 + ($submit->value/10) * 0.06) / $submit->weight)) : 0 }}</td> {{-- هزینه تمام شده --}}
                            @endif
                            <td>{{ $submit->archiveLegal ? $legal_weight = $submit->archiveLegal->weight : $legal_weight = 0 }}</td> {{-- اقلام به کیلو (صنفی) --}}
                            <td>{{ $submit->weight ? round($legal_weight / $submit->weight * 100) : $legal_weight = 0 }}</td> {{-- درصد وزن صنفی --}}
                            <td>{{ $submit->weight - $legal_weight }}</td> {{-- اقلام به کیلو (شهروندی) --}}
                            @if($gateFull)
                                <td>{{ $submit->archiveApp ? $app_weight = $submit->archiveApp->weight : $app_weight = 0 }}</td> {{-- اقلام به کیلو (اپ) --}}
                                <td>{{ $submit->weight ? round($app_weight / $submit->weight * 100) : $app_weight = 0 }}</td> {{-- درصد وزن اپ --}}
                                <td>{{ $submit->weight - $app_weight }}</td> {{-- اقلام به کیلو تلفنی --}}
                            @endif
                        @endif
                        @if($gateFull or auth()->user()->getRole('name') == 'financial_manager')
                            <td>{{ number_format($submit->value/10) }}</td> {{-- ارزش کل --}}
                        @endif
                        @if($gateFull)
{{--                            <td>{{ number_format($submit->value/10) }}</td> --}}{{-- ارزش کل --}}
                            <td>{{ number_format(($submit->user_pay)/10) }}</td> {{-- سهم شهروند --}}
                            @if (auth()->user()->getRole('name') !== 'supervisor')
                                @php
                                    $user_pay = $submit->archiveApp ?  $submit->archiveApp->user_pay : 0;
                                @endphp
                                <td>{{ number_format($user_pay/10) }}</td> {{-- شهم شهروند اپ --}}
                                <td>{{ number_format(($submit->user_pay - $user_pay)/10) }}</td> {{-- سهم شهروند تلفنی --}}
                            @endif
                            <td>{{ number_format(($submit->value * 0.05)/10) }}</td> {{-- سهم سازمان --}}
                            {{--<td>{{ number_format($submit->fava_pay/10) }}</td>--}} {{-- مبلغ پرداختی به سازمان --}}
                        @endif
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="cr-card-footer">
    </div>
    {{toast($errors)}}
</div>
