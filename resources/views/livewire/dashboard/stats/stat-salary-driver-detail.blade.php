<div>
    <div class="cr-overlay"></div>
    <div class="cr-layout-section">
        <livewire:dashboard.layouts.sidebar />
        <livewire:dashboard.layouts.navbar :$breadCrumb />

        <div class="cr-container-section">
            <div class="cr-card">
                <div class="cr-card-header">
                    <div class="cr-title">
                        <div>
                            <strong> اطلاعات حقوق راننده  {{$this->driver->name.' '.$this->driver->lastname}}</strong>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="cr-filter-section">
                        <div class="row">
                            <div class="col-lg-12 col-12">
                                <livewire:dashboard.stats.stat-salary-driver-index-filter/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="cr-card-body p-0">
                    <div class="table-responsive text-center text-nowrap">
                        <div wire:loading.class="cr-parent-spinner">
                            {{spinner()}}
                        </div>
                        @php
                        $salary=0;
                        $price_weight_avg=0;
                        $weights=0;
                        $count=0;
                        $distance=0;
                        @endphp
                        <table class="cr-table table">
                            <thead>
                            <tr>
                                <th>شناسه</th>
                                <th>شناسه درخواست</th>
                                <th>حاضر به کار</th>
                                <th>میانگین مسافت</th>
                                <th>وزن</th>
                                <th>هزینه کیلو</th>
                                <th>اتومات (حقوق)</th>
                                <th>شارژ دستی</th>
                                <th>تاریخ</th>
                            </tr>
                            </thead>
                            @if(count($this->detail->items()) > 0)
                                <tbody>
                                @foreach($this->detail->items() as $detail)
                                    <tr wire:key="{{$detail->id}}">
                                        <td>{{$detail->id}}</td>
                                        <td>{{$detail->submit_id}}</td>
                                        <td>{{$detail->total_attendance??0}}</td>
                                        <td>{{$detail->distance??0}}</td>
                                        <td>{{$detail->weight??0}} کیلوگرم </td>
                                        <td>{{$detail->weight_price??0}} تومان </td>
                                        <td>{{number_format($detail->reward_price??0)}} تومان </td>
                                        <td>{{$detail_deposite[$detail->created_at]??0}}</td>
                                        <td>  <span>{{ \Verta::instance($detail->created_at)->format('Y/m/d') }}</span></td>
                                    </tr>
                                @php
                                    $salary=$salary+$detail->reward_price;
                                    $price_weight_avg=$price_weight_avg+$detail->weight_price;
                                    $count=$count+1;
                                    $weights=$weights+$detail->weight;
                                    $distance=$distance+$detail->distance;
                                @endphp
                                @endforeach
                            @endif
                                <tr>
                                    <td colspan="3"></td>
                                    <td>میانگین مسافت : {{$count>0 ? number_format($distance/$count):0}}</td>
                                    <td>جمع وزن:{{number_format($weights)}}</td>
                                    <td>میانگین :{{($salary>0 and $weights > 0 )? number_format($salary/$weights):0}}</td>
                                    <td>جمع :{{number_format($salary)}}</td>
                                    <td colspan="2"></td>
                                </tr>
                        </table>
                        <div class="float-end"></div>
                        <div class="float-end"></div>

                    </div>
                </div>
                <div class="cr-card-footer">
                    {{$this->detail->links(data: ['scrollTo' => '#paginated-list'])}}
                </div>
            </div>
            <livewire:dashboard.layouts.footer/>
        </div>
    </div>
    {{toast($errors)}}
</div>
