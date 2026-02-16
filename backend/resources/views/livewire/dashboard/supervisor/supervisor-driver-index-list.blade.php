
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
                    <th>موبایل</th>
                    <th>نوع خودرو</th>
                    <th>پلاک خودرو</th>
                    <th>تاریخ ثبت نام</th>
                    <th>حضور ماه جاری</th>
                    <th>جزئیات</th>
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
                                <a href="{{route('d.supervisor.drivers.single',$driver->id)}}" class="cr-name">{{$driver->name.' '.$driver->lastname}}</a>
                            </td>
                            <td>{{$driver->mobile}}</td>
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
                            <td>
                                <span>{{ \Verta::instance($driver->created_at)->format('H:i') }}</span>
                                <span>{{ \Verta::instance($driver->created_at)->format('Y/m/d') }}</span>
                            </td>
                            @php($rollCall = $driver->rollCallCurrentMonth())
                            <td>{{$rollCall->hour.':'.$rollCall->min}}</td>
                            <td>
                                <a href="{{route('d.supervisor.drivers.single',$driver->id)}}" class="cr-edit">
                                    <i class="bx bx-show"></i>
                                </a>
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
