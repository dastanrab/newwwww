<div>
    <div class="cr-overlay"></div>
    <div class="cr-layout-section">
        <livewire:dashboard.layouts.sidebar />
        <livewire:dashboard.layouts.navbar :$breadCrumb />

        <div class="cr-container-section">
            <div class="row">
                {{--اطلاعات راننده--}}
                <div class="col-12">
                    <div class="cr-card">
                        <div class="cr-card-header">
                            <div class="cr-title">
                                <div>
                                    <strong>اطلاعات راننده</strong>
                                </div>
                            </div>
                        </div>
                        <div class="cr-card-body p-0">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-lg-3 col-7">
                                        <div class="cr-text cr-icon cr-md mb-3">
                                            <input type="text" id="name" value="{{$driver->name.' '.$driver->lastname}}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-5">
                                        <div class="cr-text cr-icon cr-md mb-3">
                                            <input type="text" id="type" value="{{$type}}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-6">
                                        <div class="cr-text cr-icon cr-md mb-3">
                                            <input type="text" id="mobile" value="{{$driver->mobile}}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-6">
                                        <div class="cr-plate">
                                        <span class="cr-id">
                                            <span class="font-size-12">ایران</span>
                                            <span>{{$plaque4}}</span>
                                        </span>
                                            <span class="cr-number">{{$plaque3.' '.$plaque2.' '.$plaque1}}</span>
                                            <span class="cr-flag">
                                            <img src="{{asset('assets/img/iran.png')}}" alt="" class="img-fluid">
                                            <i>I.R.</i>
                                            <i>IRAN</i>
                                        </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="cr-card-footer">
                        </div>
                    </div>
                </div>
                {{--درخواست های جاری--}}
                <div class="col-12">
                    <div class="cr-card">
                        <div class="cr-card-header">
                            <div class="cr-title">
                                <div>
                                    <strong>درخواست های جاری</strong>
                                </div>
                            </div>
                        </div>

                        <div class="cr-card-body p-0">
                            <div class="table-responsive text-center text-nowrap">
                                <div wire:loading.class="cr-parent-spinner">
                                    {{spinner()}}
                                </div>
                                @if($this->submits->count())
                                    <table class="cr-table table">
                                        <thead>
                                        <tr>
                                            <th>شناسه</th>
                                            <th>وضعیت</th>
                                            <th>اطلاعات</th>
                                            <th>کاربر</th>
                                            <th>نوع</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($this->submits as $submit)
                                            <tr wire:key="{{$submit->id}}">
                                                <td>
                                                    @if($submit->user->isFirstSubmit())
                                                        <div class="cr-rating">
                                                            <i class="bx bxs-star"></i>
                                                        </div>
                                                    @elseif($submit->status == 3 && Submit::where('user_id', $submit->user_id)->where('status', 3)->count() == 1)
                                                        <div class="cr-rating">
                                                            <i class="bx bxs-star"></i>
                                                        </div>
                                                    @endif
                                                    <div>{{$submit->id}}</div>
                                                </td>
                                                <td>
                                                    <div class="cr-circle
                                @if (\Carbon\Carbon::parse($submit->end_deadline) < now()->addHour())
                                    {{'cr-red'}}
                                @elseif (\Carbon\Carbon::parse($submit->end_deadline) < now()->addHours(4))
                                    {{'cr-yellow'}}
                                @endif"></div>
                                                </td>
                                                <td>
                                                    <div class="cr-info">
                                                        <ul>
                                                            @if($submit->is_instant)
                                                                <li class="text-danger blink">
                                                                    <i class='bx bx-run'></i>
                                                                    <span>فوری</span>
                                                                </li>
                                                            @endif
                                                            <li>
                                                                <i class="bx bxs-calendar-event"></i>
                                                                <span>{{ \Verta::instance($submit->start_deadline)->format('Y/n/j') }}</span>
                                                            </li>
                                                            <li>
                                                                <i class="bx bxs-time"></i>
                                                                <span> {{ \Verta::instance($submit->start_deadline)->format('H:i') }} الی {{ \Verta::instance($submit->end_deadline)->format('H:i')}}</span>
                                                            </li>
                                                            <li>
                                                                <i class="bx bxs-map-pin"></i>
                                                                <span>{{ xDistrict([$submit->address->lat, $submit->address->lon]) }}</span>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="cr-address">
                                                        <a class="uk-link-reset" href="{{ route('d.address.edit', [$submit->address->id, 'city' => request()->query('city')]) }}"
                                                           target="_blank">
                                                            {{ $submit->address->address }}
                                                        </a>
                                                    </div>
                                                </td>
                                                <td>

                                                    @if ($submit->user)
                                                        <a href="{{route('d.users.single',$submit->user->id)}}" class="cr-name">
                                                            @if ($submit->submit_phone)
                                                                <i class='bx bxs-phone'></i>
                                                            @endif
                                                            {{$submit->user->name.' '.$submit->user->lastname}}</a>
                                                    @endif
                                                    @if($submit->registrant && $submit->registrant_id != $submit->user_id)
                                                        <a href="#" class="cr-name">ثبت کننده: {{$submit->registrant->name.' '.$submit->registrant->lastname}}</a>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($submit->user->legal)
                                                        <i class="bx bxs-store"></i>
                                                        <div>{{$submit->user->guild_title}}</div>
                                                    @else
                                                        <i class="bx bxs-user-circle"></i>
                                                    @endif
                                                </td>
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
                        </div>
                    </div>
                </div>
                {{--درخواست های انجام شده--}}
                <div class="col-12">
                    <div class="cr-card">
                        <div class="cr-card-header">
                            <div class="cr-title">
                                <div>
                                    <strong>درخواست های انجام شده</strong>
                                </div>
                            </div>
                        </div>
                        <div id="paginated-list">
                            <div class="cr-card-body p-0">
                                <div class="table-responsive text-center text-nowrap">
                                    <div wire:loading.class="cr-parent-spinner">
                                        {{spinner()}}
                                    </div>
                                    @if($this->submitsDone->count())
                                        <table class="cr-table table" >
                                            <thead>
                                            <tr>
                                                <th>شناسه</th>
                                                <th>نام و نام خانوادگی</th>
                                                <th>شهروندی</th>
                                                <th>آدرس</th>
                                                <th>شیوه درخواست</th>
                                                <th>نوع درخواست</th>
                                                <th>تاریخ ثبت درخواست</th>
                                                <th>تاریخ درخواست جمع آوری</th>
                                                <th>تاریخ جمع آوری شده</th>
                                                <th>تاریخ اولین پسماند ثبت شده</th>
                                                <th>اقلام جمع آوری شده</th>
                                                <th>مقدار اقلام تحویلی</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($this->submitsDone as $submit)
                                                <tr wire:key="{{$submit->id}}">
                                                    <td>{{$submit->id}}</td>
                                                    <td>
                                                        <a href="{{route('d.users.single',$submit->user->id)}}" class="cr-name" target="_blank">
                                                            {{$submit->user->name || $submit->user->lastname ? $submit->user->name.' '.$submit->user->lastname : $submit->user->guild_title}}
                                                        </a>
                                                    </td>
                                                    <td>{{ $submit->user->legal == 0 ? 'شهروندی' : 'صنفی' }}</td>
                                                    <td>
                                                        <div class="cr-actions">
                                                            <ul>
                                                                <li><a href="" data-bs-toggle="modal" data-bs-target="#submit-address-{{$submit->id}}"><i class="bx bx-map"></i></a></li>
                                                            </ul>
                                                        </div>
                                                        <!-- Modal -->
                                                        <div class="modal fade" id="submit-address-{{$submit->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="exampleModalLabel">آدرس درخواست</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p>{{$submit->address->address}}</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>{{ $submit->submit_phone ? 'تلفنی' : 'اپلیکیشن' }}</td>
                                                    <td class="dir-ltr">{{ $submit->is_instant == 1 ? 'فوری' : 'زمان‌بندی‌شده' }}</td>
                                                    <td class="dir-ltr">{{ \Verta::instance($submit->created_at)->format('Y/m/d H:i:s') }}</td>
                                                    <td class="dir-ltr">{{ \Verta::instance($submit->start_deadline)->format('Y/m/d H:i').'-'.\Verta::instance($submit->end_deadline)->format('H:i') }} </td>
                                                    <td class="dir-ltr">
                                                        {{ $submit->drivers[0]->receives->count() ? \Verta::instance($submit->drivers[0]->collected_at)->format('Y/m/d') : ''}}
                                                        <br>
                                                        {{ $submit->drivers[0]->receives->count() ? \Verta::instance($submit->drivers[0]->collected_at)->format('H:i:s') : ''}}
                                                    </td>
                                                    <td class="dir-ltr">
                                                        {{ $submit->drivers[0]->receives->count() ? \Verta::instance($submit->drivers[0]->receives[0]->updated_at)->format('Y/m/d') : ''}}
                                                        {{ $submit->drivers[0]->receives->count() ? \Verta::instance($submit->drivers[0]->receives[0]->updated_at)->format('H:i:s') : ''}}
                                                    </td>
                                                    <td>
                                                        @foreach ($submit->drivers[0]->receives as $receive){{ $receive->title }}({{ weightFormat($receive->weight) }})<br> @endforeach
                                                        @if($submit->status == 3)
                                                            <div class="cr-actions">
                                                                <ul>
                                                                    <li><a href="#" data-bs-toggle="modal" data-bs-target="#detail-receive-{{$submit->id}}"><i class='bx bx-basket'></i></a></li>
                                                                </ul>
                                                            </div>
                                                            <!-- Modal -->
                                                            <div class="modal fade" id="detail-receive-{{$submit->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                                <div class="modal-dialog modal-lg">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title" id="exampleModalLabel">جزییات پسماندها</h5>
                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <table class="table table-responsive">
                                                                                <tr>
                                                                                    <th>پسماند</th>
                                                                                    <th>وزن (کیلو)</th>
                                                                                    <th>قیمت واحد (تومان)</th>
                                                                                    <th>قیمت کل (تومان)</th>
                                                                                </tr>
                                                                                @php
                                                                                    $totalWeight = 0;
                                                                                    $totalPriceWeight = 0;
                                                                                @endphp
                                                                                @foreach ($submit->drivers[0]->receives as $receive)
                                                                                    @php
                                                                                        $totalWeight      += $receive->weight;
                                                                                        $totalPriceWeight += $receive->price*$receive->weight
                                                                                    @endphp
                                                                                    <tr>
                                                                                        <td>{{ $receive->title }}</td>
                                                                                        <td>{{weightFormat($receive->weight)}}</td>
                                                                                        <td>{{number_format($receive->price)}}</td>
                                                                                        <td>{{number_format($receive->price*$receive->weight)}}</td>
                                                                                    </tr>
                                                                                @endforeach
                                                                                <tr>
                                                                                    <td>کل</td>
                                                                                    <td>{{{weightFormat($totalWeight)}}}</td>
                                                                                    <td>-</td>
                                                                                    <td>{{number_format($totalPriceWeight)}}</td>
                                                                                </tr>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>{{ weightFormat($submit->drivers[0]->receives->pluck('weight')->sum()) }}</td>
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
                            </div>
                            @script
                            <script>
                                $(document).ready(function (){

                                    $('[data-bs-toggle="tooltip"]').tooltip()
                                })

                            </script>
                            @endscript
                            {{toast($errors)}}
                        </div>
                    </div>
                </div>
                {{--جمع بار امروز--}}
                <div class="col-12 col-md-6">
                    <div class="cr-card p-0">
                        <div class="cr-card-header">
                            <div class="row align-items-baseline">
                                <div class="col-md-5 col-12">
                                    <div class="cr-title">
                                        <div>
                                            <strong>جمع بار امروز</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive text-center text-nowrap">
                            <div class="row">
                                <div class="col-12 col-lg-6">
                                    <table class="cr-table table">
                                        <thead>
                                        <tr>
                                            <th>پسماند</th>
                                            <th>وزن</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @php
                                            foreach ($this->recyclables as $recyclable){
                                                $weight[$recyclable->id] = 0;
                                            }
                                        @endphp
                                        @foreach($this->recyclables as $recyclable)
                                            @foreach($this->collected as $collect)
                                                @php
                                                    $weight[$recyclable->id] += $collect->receives ? $collect->receives->where('fava_id', $recyclable->id)->pluck('weight')->sum() : 0;
                                                @endphp
                                            @endforeach
                                            @if($weight[$recyclable->id] > 0)
                                                <tr>
                                                    <td>{{$recyclable->title}}</td>
                                                    <td>{{weightFormat($weight[$recyclable->id])}}</td>
                                                </tr>
                                            @endif
                                        @endforeach

                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-12 col-lg-6">

                                    <table class="cr-table table">
                                        <thead>
                                        <tr>
                                            <th>کلی</th>
                                            <th>وزن</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>کاغذ و کارتن</td>
                                            <td>{{weightFormat($weight[1])}}</td>
                                        </tr>
                                        <tr>
                                            <td>فلزات</td>
                                            <td>{{weightFormat($weight[6]+$weight[7]+$weight[13]+$weight[18])}}</td>
                                        </tr>
                                        <tr>
                                            <td>سایر</td>
                                            <td>{{weightFormat($weight[2]+$weight[3]+$weight[4]+$weight[5]+$weight[8]+$weight[9]+$weight[10]+$weight[11]+$weight[12]+$weight[14]+$weight[15]+$weight[16]+$weight[17]+$weight[19]+$weight[20]+$weight[21])}}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>جمع کل</strong></td>
                                            @php
                                                $weightAll = $weight[1]+$weight[2]+$weight[3]+$weight[4]+$weight[5]+$weight[6]+$weight[7]+$weight[8]+$weight[9]+$weight[10]+$weight[11]+$weight[12]+$weight[13]+$weight[14]+$weight[15]+$weight[16]+$weight[17]+$weight[18]+$weight[19]+$weight[20]+$weight[21];
                                            @endphp
                                            <td><strong>{{weightFormat($weightAll)}}</strong></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <table class="cr-table table">
                                <thead>
                                <tr>
                                    <th>پرداخت</th>
                                    <th>مبلغ (تومان)</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>پرداختی</td>
                                    @php
                                        $amount_user = 0;
                                        foreach($this->collected as $collect){
                                            $amount_user += $collect->submit->total_amount;
                                        }
                                    @endphp
                                    <td>{{number_format(floor($amount_user))}}</td>
                                </tr>
                                @php $amount_fava = 0 @endphp
                                {{--<tr>
                                    <td>پرداختی ۲</td>
                                    @php
                                        $amount_fava = 0;
                                        foreach($this->collected as $collect){
                                            $amount_fava += $amount_fava += \App\Models\AsanPardakht::where('type_id', $collect->id)->where('type', 'submit_fava')->pluck('amount')->sum();
                                        }
                                    @endphp
                                    <td>{{number_format(floor($amount_fava/10))}}</td>
                                </tr>--}}
                                {{--<tr>
                                    <td><strong>جمع پرداختی</strong></td>
                                    <td><strong>{{number_format(floor($amount_fava+$amount_user))}}</strong></td>
                                </tr>--}}
                                <tr>
                                    <td><strong>میانگین</strong></td>
                                    <td>{{$amount_user + $amount_fava ? number_format(($amount_user + $amount_fava)/$weightAll) : 0}}</td>
                                </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
                {{--ردیابی--}}
                <div class="col-12 col-md-6">
                    <div class="cr-card">
                        <div class="cr-card-header">
                            <div class="row align-items-baseline">
                                <div class="col-md-5 col-12">
                                    <div class="cr-title">
                                        <div>
                                            <strong>ردیابی آنلاین</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="cr-card-body p-0">
                            <div class="cr-map-section" id="cr-map-section"></div>
                        </div>
                        <div class="cr-card-footer p-0">
                        </div>
                    </div>
                </div>
            </div>
            <livewire:dashboard.layouts.footer/>
        </div>


    </div>
    {{toast($errors)}}
</div>
@script
<script>

    if($('#cr-map-section').length) {
        map = new L.Map("cr-map-section", {
            key: 'web.3dd03927cf104d018a4ce3cd6ec3c962',
            maptype: 'dreamy',
            center: [36.2966309, 59.6029849],
            zoom: 12,
            poi: true,
            traffic: false,
            zoomControl: false,
        });

        let locations;
        let polyLinesLatLng = [];
        locations = {!! json_encode($this->locations) !!};
        $.each( locations, function( i, loc ) {
            polyLinesLatLng[i] = [loc.lat, loc.long];
        });
        let polyline = L.polyline(polyLinesLatLng, {color: 'red'}).arrowheads({size: '5px'}).addTo(map);
        map.fitBounds(polyline.getBounds());

        let marker = L.marker(
            [
                locations[polyLinesLatLng.length-1].lat,
                locations[polyLinesLatLng.length-1].long
            ]
        ).bindPopup('<div class="text-end"><strong>'+locations[polyLinesLatLng.length-1].name+' '+locations[polyLinesLatLng.length-1].lastname+'<br>'+'<span class="dir-ltr">آخرین اتصال: '+locations[polyLinesLatLng.length-1].date+'</span></strong></div>', {autoClose:false}).addTo(map).openPopup();


    }

</script>
@endscript
