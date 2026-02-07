@php
    $gateDriverIndexRollCall = Gate::allows('user_driver_index_rollcall',App\Models\User::class);
    $gateDriverSingle = Gate::allows('user_driver_single',App\Models\User::class);
    $gateDriverCar = Gate::allows('user_driver_index_car',App\Models\User::class);
    $gateDriverMobile = Gate::allows('user_driver_index_mobile',App\Models\User::class);
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
                    <th>شناسه</th>
                    @if($gateDriverIndexRollCall)
                        <th>حضور</th>
                    @endif
                    <th>موقعیت</th>
                    <th>وضعیت</th>
                    <th>راننده</th>
                    @if($gateDriverMobile)
                        <th>موبایل</th>
                    @endif
                    @if($gateDriverCar)
                        <th>نوع خودرو</th>
                        <th>پلاک خودرو</th>
                    @endif
                    <th>تاریخ ثبت نام</th>
                    <th>وضعیت</th>
                    <th>حضور ماه جاری</th>
                    <th>گردش حساب</th>
                    @if($gateDriverSingle)
                    <th>ویرایش</th>
                    @endif
                </tr>
                </thead>
                @if($this->drivers->count())
                    <tbody>
                    @foreach($this->drivers as $driver)
                        <tr wire:key="{{$driver->id}}">
                            <td>{{$driver->id}}</td>
                            @if($gateDriverIndexRollCall)
                                <td>
                                    <a href="{{route('d.drivers.rollcall',$driver->id)}}" class="cr-edit">
                                        <i class='bx bxs-hand' ></i>
                                    </a>
                                </td>
                            @endif
                            <td>
                                <a href="{{route('d.driver.map',$driver->id)}}"   data-bs-toggle="tooltip" title="نمایش جزئیات" id="">
                                    <i class="bx bxs-location-plus"></i>
                                </a>
                            </td>
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
                                @if($gateDriverSingle)
                                <a href="{{route('d.drivers.single',$driver->id)}}" class="cr-name">{{$driver->name.' '.$driver->lastname}}</a>
                                @else
                                    {{$driver->name.' '.$driver->lastname}}
                                @endif
                            </td>
                            @if($gateDriverMobile)
                                <td>{{$driver->mobile}}</td>
                            @endif
                            @if($gateDriverCar)
                                <td>{{$driver->cars->first()->type}}</td>
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
                            @endif
                            <td>
                                <span>{{ \Verta::instance($driver->created_at)->format('H:i') }}</span>
                                <span>{{ \Verta::instance($driver->created_at)->format('Y/m/d') }}</span>
                            </td>
                            <td>
                                @if($driver->cars->first()->is_active)
                                <div class="cr-active">
                                    <i class='bx bxs-message-square-check'></i>
                                </div>
                                @else
                                <div class="cr-inactive">
                                    <i class='bx bxs-message-square-x'></i>
                                </div>
                                @endif
                            </td>
                            @php($rollCall = $driver->rollCallCurrentMonth())
                            <td>{{$rollCall->hour.':'.$rollCall->min}}</td>
                            <td>
                                <a href="{{route('d.wallet')}}?user_id={{$driver->id}}"   data-bs-toggle="tooltip" title="نمایش جزئیات" id="">
                                    <i class="bx bxs-wallet"></i>
                                </a>
                            </td>
                            @if($gateDriverSingle)
                                <td>
                                    <a href="{{route('d.drivers.single',$driver->id)}}" class="cr-edit">
                                        <i class="bx bxs-message-square-edit"></i>
                                    </a>
                                </td>
                            @endif
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
