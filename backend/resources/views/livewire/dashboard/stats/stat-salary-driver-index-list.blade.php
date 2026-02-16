<div id="paginated-list">
    <div class="cr-card-body p-0">
        <div class="table-responsive text-center text-nowrap">
            <div wire:loading.class="cr-parent-spinner">
                {{spinner()}}
            </div>
            <table class="cr-table table">
                <thead>
                <tr>
                    <th>شناسه</th>
                    <th>وضعیت</th>
                    <th>راننده</th>
                    <th>حاضر به کار</th>
                    <th>میانگین مسافت</th>
                    <th>پلاک خودرو</th>
                    <th>شارژ دستی</th>
                    <th>وزن</th>
                    <th>هزینه کیلو</th>
                    <th>اتومات (حقوق)</th>
                    <th>اتومات (حقوق) ارسال شده</th>
                    <th>واریز شده</th>
                    <th>مانده</th>
                    <th>واریز</th>
                </tr>
                </thead>
                @if($this->drivers->count())
                    <tbody>
                    @foreach($this->drivers as $driver)
                        <tr wire:key="{{$driver->id}}">
                            <td>{{$driver->id}}</td>
                            @if ($driver->car->rollcall_status == 1)
                                <td data-bs-toggle="tooltip" title="فعال - غایب">
                                    <div class="cr-offline">
                                        <i></i>
                                    </div>
                                </td>
                            @elseif ($driver->car->rollcall_status == 2)
                                <td data-bs-toggle="tooltip" title="فعال - حاضر">
                                    <div class="cr-online">
                                        <i></i>
                                    </div>
                                </td>
                            @else
                                <td data-bs-toggle="tooltip" title="غیرفعال">
                                    <div class="cr-deactivate">
                                        <i></i>
                                    </div>
                                </td>
                            @endif
                            <td>
                                @php
                                    $queryString = (isset($this->dateFrom) and isset($this->dateTo)) ? "?dateFrom=".$this->dateFrom.'&dateTo='.$this->dateTo : '';
                                @endphp
                                    <a href="{{route('d.stats.salary-driver-detail',$driver->id).$queryString}}" class="cr-name">{{$driver->name.' '.$driver->lastname}}</a>
                            </td>
                            <td>{{number_format(isset($this->details[$driver->id])?$this->details[$driver->id]['total_attendance']:0,4)}}
                            دقیقه</td>
                            <td>{{number_format(isset($this->details[$driver->id])?$this->details[$driver->id]['distance']/1000:0,4)}}
                                کیلو متر
                            <td>
                                    @if($driver->cars->first()->plaque_4)
                                        <div class="cr-plate">
                                            <span class="cr-id"><span class="font-size-12">ایران</span><span>{{$driver->cars->first()->plaque_4}}</span></span>
                                            <span class="cr-number">{{ $driver->cars->first()->plaque_3.' '.$driver->cars->first()->plaque_2.' '.$driver->cars->first()->plaque_1 }}</span>
                                            <span class="cr-flag"><img src="{{asset('assets/img/iran.png')}}" alt="" class="img-fluid"><i>I.R.</i><i>IRAN</i></span>
                                        </div>
                                    @else
                                        -
                                    @endif
                                </td>
                            <td>{{number_format(isset($this->wallet_deposite[$driver->wallet->id])?$this->wallet_deposite[$driver->wallet->id]->amount/10:0)}} تومان </td>
                            <td>{{number_format(isset($this->details[$driver->id])?$this->details[$driver->id]['weight']:0)}} کیلوگرم </td>
                            <td>{{number_format(isset($this->details[$driver->id])?$this->details[$driver->id]['weight_price']:0)}} تومان </td>
                            <td>{{number_format(isset($this->details[$driver->id])?$this->details[$driver->id]['reward_price']:0)}} تومان </td>
                            <td>{{number_format(isset($this->salary_pays[$driver->id])?$this->salary_pays[$driver->id]['amount']:0)}} تومان </td>
                            <td>0 تومان </td>
                            <td>{{number_format($driver->wallet->wallet)}} تومان </td>
                            <td>
                                <button class="btn btn-sm btn-primary text-bg-primary" data-bs-toggle="tooltip" title="واریز پاداش راننده" >
                                    <i class='bx bx-wallet'></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                @endif
            </table>
        </div>
    </div>
    <div class="cr-card-footer">
        {{$this->drivers->links(data: ['scrollTo' => '#paginated-list'])}}
    </div>
</div>
