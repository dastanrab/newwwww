@php use App\Models\BazistWallet;use App\Models\Driver; @endphp
<div>
    <div class="cr-card">
        <div class="cr-card-header">
            <div class="cr-title">
                <div>
                    <strong>فیلتر گردش حساب</strong>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="cr-filter-section mt-2">
                <div class="row">
                    <div class="col-md-6 col-12">
                        <div class="cr-select" id="filter">
                            <select wire:model.live="filter">
                                <option value="">همه</option>
                                <option value="asanpardakht">آپ</option>
                                <option value="bazist">بازیست</option>
                                <option value="cashouts">واریز به کارت</option>
                                <option value="charge_internet">شارژ و اینترنت (آینکس)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <table class="cr-table table mt-2">
                    <thead>
                    <tr>
                        <th>شناسه</th>
                        <th>نام و نام خانوادگی</th>
                        @if(!in_array(auth()->user()->getRoles(0), ['accountants', 'financial_manager']))
                            <th>شماره همراه</th>
                        @endif
                        <th>گروه کاربری</th>
                        <th>جمع پسماندها</th>
                        <th>جمع پاداش ها</th>
                        <th>جمع شارژ و اینترنت</th>
                        <th>موجودی</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>{{$user->id}}</td>
                        <td>
                            @if($user->level == 2)
                                {!! levelIcon() !!}
                            @endif
                            {{$user->name.' '.$user->lastname}}
                        </td>
                        @if(!in_array(auth()->user()->getRoles(0), ['accountants', 'financial_manager']))
                            <td>{{$user->mobile}}</td>
                        @endif
                        <td>{{$user->getRoleName()}} - {{$user->getLegalType()}}</td>
                        <td>{{number_format(\App\Models\Submit::where('user_id',$user->id)->whereNot('cashout_type','aap')->where('status',3)->sum('total_amount'))}}</td>
                        <td>{{number_format($this->user_paadaash)}}</td>
                        <td>{{number_format(BazistWallet::where('user_id',$user->id)->whereIn('type', ['sharj_internet', 'sharj_mobile'])->sum('amount')/10)}}</td>
                        <td>{{number_format(floor($user->wallet->wallet))}} تومان</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="cr-card-footer">
        </div>
    </div>
    <div class="cr-card" id="ballance-list">
        <div class="cr-card-header">
            <div class="cr-title">
                <div>
                    <strong>بالانس حساب</strong>
                </div>
            </div>
        </div>
        <div>
            <div class="cr-card-body p-0">
                <div class="table-responsive text-center text-nowrap">
                    <div wire:loading.class="cr-parent-spinner">
                        {{spinner()}}
                    </div>
                    @if($this->ballance->count())
                        <table class="cr-table table">
                            <thead>
                            <tr>
                                <th>مجموع واریز</th>
                                <th> مجموع برداشت </th>
                                <th>بالانس حساب</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($this->ballance as $item)
                                <tr >
                                    <td>{{number_format($item->vaariz_amount/10)}}</td>
{{--                                    <td>{{number_format($this->inax_bardasht+$this->aap_bardasht+$this->cashout_bardasht)}}</td>--}}
                                    <td>{{number_format($item->bardaasht_amount/10)}}</td>
                                    <td>{{number_format($item->balance_amount/10)}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        @include('livewire.dashboard.layouts.data-not-exists')
                    @endif

                </div>
            </div>
        </div>

    </div>
    <div class="clearfix"></div>
    <div class="cr-card" id="total-deposite-list">
        <div class="cr-card-header">
            <div class="cr-title">
                <div>
                    <strong>مجموع بابت واریزی ها</strong>
                </div>
            </div>
        </div>
        <div>
            <div class="cr-card-body p-0">
                <div class="table-responsive text-center text-nowrap">
                    <div wire:loading.class="cr-parent-spinner">
                        {{spinner()}}
                    </div>
                        <table class="cr-table table">
                            <thead>
                            <tr>
                                <th>مجموع(واریزی کیف پول - نقدی)</th>
                                <th>مجموع(ثبت نشده کیف پول - نقدی)</th>
                                <th>مجموع(واریزی شده توسط ادمین)</th>
                                <th>مجموع(واریزی آینکس برگشتی)</th>
                                <th>مجموع(واریزی برگشت بانکی)</th>
                                {{--                                <th>مجموع(واریزی شده آپ)</th>--}}
                                <th>مجموع همه</th>
                                <th>مجموع پاداش</th>
                                <th> مجموع پاداش + مجموع پسمانده ها</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr >
                                    <td>{{number_format($this->bazist_deposite_amounts)}}</td>
                                    <td>{{number_format($this->no_deposite_amounts)}}</td>
                                    <td>{{number_format($this->admin_deposite)}}</td>
                                    <td>{{number_format($this->return_inax_deposit)}}</td>
                                    {{--                                    <td>{{number_format($this->aap_deposite_amounts)}}</td>--}}
                                    <td>{{number_format($this->back_to_bazist_wallet)}}</td>
                                    <td>{{number_format( $this->back_to_bazist_wallet+$this->bazist_deposite_amounts + $this->no_deposite_amounts+$this->admin_deposite)}}</td>
                                    <td>{{number_format($this->user_paadaash)}}</td>
                                    <td>{{number_format($this->back_to_bazist_wallet+$this->user_paadaash+$this->user_waste_amounts+$this->admin_deposite+$this->return_inax_deposit)}}</td>
                                </tr>
                            </tbody>
                        </table>
                </div>
            </div>
        </div>

    </div>
    <div class="clearfix"></div>
    <div class="cr-card" id="total-removal-list">
        <div class="cr-card-header">
            <div class="cr-title">
                <div>
                    <strong>مجموع بابت برداشت ها</strong>
                </div>
            </div>
        </div>
        <div>
            <div class="cr-card-body p-0">
                <div class="table-responsive text-center text-nowrap">
                    <div wire:loading.class="cr-parent-spinner">
                        {{spinner()}}
                    </div>
                    <table class="cr-table table">
                        <thead>
                        <tr>
                            <th>مجموع برداشت توسط ادمین</th>
                            <th>مجموع برداشت (کارت بانکی)</th>
                            <th>مجموع برداشت (آسان پرداخت)</th>
                            <th>مجموع برداشت (شارژ موبایل/اینترنت)</th>
                            <th>مجموع کارمزد برداشت شده</th>
                            <th>مجموع برداشت </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr >
                            <td>{{number_format($this->admin_bardasht)}}</td>
                            <td>{{number_format($this->cashout_bardasht)}}</td>
                            <td>{{number_format($this->aap_bardasht)}}</td>
                            <td>{{number_format($this->inax_bardasht)}}</td>
                            <td>{{number_format($this->tax)}}</td>
                            <td>{{number_format($this->inax_bardasht+$this->admin_bardasht+$this->cashout_bardasht+$this->tax+$this->aap_bardasht)}}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
    <div class="clearfix"></div>
        <div class="cr-card" id="not-deposite-list">
            <div class="cr-card-header">
                <div class="cr-title">
                    <div>
                        <strong>درخواست های (کیف پول بازیست - نقدی) ثبت نشده</strong>
                    </div>
                </div>
            </div>
            <div>
                <div class="cr-card-body p-0">
                    <div class="table-responsive text-center text-nowrap">
                        <div wire:loading>
                            {{spinner()}}
                        </div>
                        @if($this->not_deposites->count())
                            <table class="cr-table table">
                                <thead>
                                <tr>
                                    <th>شناسه درخواست</th>
                                    <th>سهم شهروند درخواست</th>
                                    <th>وضعیت درخواست</th>
                                    <th>نوع پرداخت</th>
                                    <th>تاریخ</th>
                                    <th>عملیات</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($this->not_deposites as $item)
                                    <tr wire:key="{{$item->id}}">
                                        <td>{{$item->id}}</td>
                                        <td>{{number_format($item->total_amount)}} تومان</td>
                                        <td>
                                                <span>جمع آوری شده</span>
                                        </td>
                                        <td>{{ $item->cashout_type=='bazist'?'کیف پول بازیست':'پرداخت به کارت' }}</td>
                                        <td class="dir-ltr">{{\Verta::instance($item->start_deadline)->format('Y/m/d H:i')}}</td>
                                        <td>
                                            <div class="cr-actions">
                                                <ul>
                                                    <li>
                                                        <input id="d_id" type="hidden" value={{$item->id}}>
                                                        <a class="pay_submit"
                                                           title="ثبت" data-submit="{{$item->id}}">
                                                            <i class="bx bx-wallet"></i>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="float-end">جمع سهم شهروند ثبت نشده:{{number_format($this->no_deposite_amounts)}}</div>
                        @else
                            @include('livewire.dashboard.layouts.data-not-exists')
                        @endif

                    </div>
                </div>
                <div class="cr-card-footer">
                    {{ $this->not_deposites->links(data: ['scrollTo' => '#not-deposite-list']) }}
                </div>
            </div>

        </div>
        <div class="clearfix"></div>

@if(!$filter || $filter == 'asanpardakht')
        <div class="cr-card" id="ap-list">
            <div class="cr-card-header">
                <div class="cr-title">
                    <div>
                        <strong>آسان پرداخت</strong>
                    </div>
                </div>
            </div>
            <div>
                <div class="cr-card-body p-0">
                    <div class="table-responsive text-center text-nowrap">
                        <div wire:loading.class="cr-parent-spinner">
                            {{spinner()}}
                        </div>
                        @if($this->asanpardakht->count())
                            <table class="cr-table table">
                                <thead>
                                <tr>
                                    <th>شناسه</th>
                                    <th>توضیحات</th>
                                    <th>مبلغ (تومان)</th>
                                    <th>کد رهگیری</th>
                                    <th>تاریخ</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($this->asanpardakht as $item)
                                    <tr wire:key="{{$item->id}}">
                                        <td>{{$item->id}}</td>
                                        <td>{{$item->details}}</td>
                                        <td class="dir-ltr">
                                            <span>{{$item->method == 'برداشت' && $item->amount > 0 ? '-' : ''}}{{number_format($item->amount/10)}} </span>
                                        </td>
                                        <td>{{$item->rrn}}</td>
                                        <td class="dir-ltr">{{\Verta::instance($item->created_at)->format('Y/m/d H:i')}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @else
                            @include('livewire.dashboard.layouts.data-not-exists')
                        @endif

                    </div>
                </div>
                <div class="cr-card-footer">
                    {{ $this->asanpardakht->links(data: ['scrollTo' => '#ap-list']) }}
                </div>
            </div>

        </div>
        <div class="clearfix"></div>
    @endif
    @if(!$filter || $filter == 'bazist')
        <div class="cr-card" id="bazist-list">
            <div class="cr-card-header">
                <div class="cr-title">
                    <div>
                        <strong>بازیست</strong>
                    </div>
                </div>
            </div>
            <div>
                <div class="cr-card-body p-0">
                    <div class="table-responsive text-center text-nowrap">
                        <div wire:loading>
                            {{spinner()}}
                        </div>
                        @if($this->bazist->count())
                            <table class="cr-table table">
                                <thead>
                                <tr>
                                    <th>شناسه</th>
                                    <th>توضیحات</th>
                                    <th>نوع</th>
                                    <th>رفرنس</th>
                                    <th>مبلغ (تومان)</th>
                                    <th>موجودی کیف پول</th>
                                    <th>کد رهگیری</th>
                                    <th>تاریخ</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($this->bazist as $item)
                                    <tr wire:key="{{$item->id}}">
                                        <td>{{$item->id}}</td>
                                        <td>{{$item->details}}</td>
                                        <td>
                                            <span {{$item->method == 'برداشت' ? 'style=color:red':'style=color:green'}}>{{$item->method}}</span>
                                        </td>
                                        <td>
                                            @if($item->type == 'submit_user_ref' && $item->type_id)
                                                @php $driver = Driver::find($item->type_id) @endphp
                                                <a href="{{route('d.stats.submit',['id' => $driver->submit->id])}}">{{$driver->submit->id}}</a>
                                            @endif
                                        </td>
                                        <td  class="dir-ltr">
                                            <span {{$item->method == 'برداشت' ? 'style=color:red':'style=color:green'}}>{{$item->method == 'برداشت' && $item->amount > 0 ? '-' : ''}}{{number_format(floor($item->amount/10))}} </span>
                                        </td>
                                        <td class="dir-ltr">
                                            <span>{{number_format(floor($item->wallet_balance/10))}} </span>
                                        </td>
                                        <td>{{ 100000000000 + $item->id }}</td>
                                        <td class="dir-ltr">{{\Verta::instance($item->created_at)->format('Y/m/d H:i')}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @else
                            @include('livewire.dashboard.layouts.data-not-exists')
                        @endif

                    </div>
                </div>
                <div class="cr-card-footer">
                    {{ $this->bazist->links(data: ['scrollTo' => '#bazist-list']) }}
                </div>
            </div>

        </div>
        <div class="clearfix"></div>
    @endif
    @if(!$filter || $filter == 'cashouts')
        <div class="cr-card" id="cashout-list">
            <div class="cr-card-header">
                <div class="cr-title">
                    <div>
                        <strong>واریز به کارت</strong>
                    </div>
                </div>
            </div>
            <div>
                <div class="cr-card-body p-0">
                    <div class="table-responsive text-center text-nowrap">
                        <div wire:loading>
                            {{spinner()}}
                        </div>
                        @if($this->cashouts->count())
                            <table class="cr-table table">
                                <thead>
                                <tr>
                                    <th>شناسه</th>
                                    <th>مبلغ</th>
                                    <th>وضعیت</th>
                                    <th>کد رهگیری</th>
                                    <th>تاریخ</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($this->cashouts as $item)
                                    <tr wire:key="{{$item->id}}">
                                        <td>{{$item->id}}</td>
                                        <td>{{number_format($item->amount)}} تومان</td>
                                        <td>
                                            @if($item->trace_code > 0 || $item->status == 'deposited')
                                                <span>واریز شده</span>
                                            @elseif($item->status == 'waiting')
                                                <span>درانتظار واریز</span>
                                            @elseif($item->status == 'depositing')
                                                <span>درحال واریز</span>
                                            @elseif($item->status == 'refunded')
                                                <span>برگشتی</span>
                                            @endif
                                        </td>
                                        <td>{{ $item->trace_code }}</td>
                                        <td class="dir-ltr">{{\Verta::instance($item->created_at)->format('Y/m/d H:i')}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="float-end">جمع واریزی به کارت :{{number_format($this->cashout_bardasht)}}</div>
                        @else
                            @include('livewire.dashboard.layouts.data-not-exists')
                        @endif

                    </div>
                </div>
                <div class="cr-card-footer">
                    {{ $this->cashouts->links(data: ['scrollTo' => '#cashout-list']) }}
                </div>
            </div>

        </div>
        <div class="clearfix"></div>
    @endif

    @if(!$filter || $filter == 'charge_internet')
        <div class="cr-card" id="charge-internet-list">
            <div class="cr-card-header">
                <div class="cr-title">
                    <div>
                        <strong>شارژ و اینترنت (آینکس)</strong>
                    </div>
                </div>
            </div>
            <div>
                <div class="cr-card-body p-0">
                    <div class="table-responsive text-center text-nowrap">
                        <div wire:loading>
                            {{spinner()}}
                        </div>
                        @if($this->charge_internet->count())
                            <table class="cr-table table">
                                <thead>
                                <tr>
                                    <th>شناسه</th>
                                    <th>مبلغ</th>
                                    <th>شماره</th>
                                    <th>اپراتور</th>
                                    <th>نوع</th>
                                    <th>شماره پیگیری</th>
                                    <th>کد تراکنش</th>
                                    <th>وضعیت</th>
                                    <th>توضیحات</th>
                                    <th>تاریخ</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($this->charge_internet as $item)
                                    <tr wire:key="{{$item->id}}">
                                        <td>{{$item->id}}</td>
                                        <td>{{number_format($item->amount)}}</td>
                                        <td>{{$item->mobile}}</td>
                                        <td>{{$item->operator}}</td>
                                        <td>
                                            @if($item->method == 'topup')
                                                <span>شارژ همراه</span>
                                            @elseif($item->method == 'internet')
                                                <span>اینترنت</span>
                                            @else
                                                <span>{{$item->method}}</span>
                                            @endif
                                        </td>
                                        <td>{{$item->ref_code ?? '-'}}</td>
                                        <td>{{$item->trans_id ?? '-'}}</td>
                                        <td>
                                            @if($item->status == 'cancel')
                                                <span>لغو شده</span>
                                            @elseif($item->status == 'done')
                                                <span>انجام شده</span>
                                            @endif
                                        </td>
                                        <td>{{$item->description ?? '-'}}</td>
                                        <td class="dir-ltr">{{\Verta::instance($item->created_at)->format('Y/m/d H:i')}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @else
                            @include('livewire.dashboard.layouts.data-not-exists')
                        @endif

                    </div>
                </div>
                <div class="cr-card-footer">
                    {{ $this->cashouts->links(data: ['scrollTo' => '#cashout-list']) }}
                </div>
            </div>

        </div>
        <div class="clearfix"></div>
    @endif
    @script
    <script>
        jQuery(document).ready(function($) {

            $(document).on('click', '.pay_submit', function(e) {
                let d_id = $('#d_id').val()
                e.preventDefault();
                Swal.fire({
                    title: 'ثبت درخواست ثبت نشده',
                    text: 'آیا از ثبت واریز مبلغ درخواست کاربر اطمینان دارید؟',
                    icon: 'success',
                    showCloseButton: true,
                    showCancelButton: true,
                    cancelButtonText: 'خیر',
                    confirmButtonText: 'بله',
                }).then((result) => {
                    console.log(result)
                    if (result.isConfirmed) {
                        $wire.dispatch('pay', { submit: d_id});
                    }
                });
            });
        });
    </script>
    @endscript
    {{toast($errors)}}
</div>
