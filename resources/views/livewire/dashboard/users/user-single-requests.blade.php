<div class="cr-card" id="paginated-requests">
    <div class="cr-card-header">
        <div class="cr-title mb-0">
            <div>
                <strong>درخواست ها</strong>
            </div>
        </div>
    </div>
    <div class="cr-card-body p-0">
        <div class="table-responsive text-center text-nowrap">
            <div wire:loading.class="cr-parent-spinner">
                {{spinner()}}
            </div>
            <table class="cr-table table">
                <thead id="tableHeader">
                <tr>
                    <th>شناسه</th>
                    <th>شناسه فاوا</th>
                    <th>آدرس</th>
                    <th>شیوه درخواست</th>
                    <th>نوع درخواست</th>
                    <th>تاریخ درخواست</th>
                    <th>شروع زمان درخواست جمع‌آوری</th>
                    <th>پایان زمان درخواست جمع‌آوری</th>
                    <th>وضعیت درخواست</th>
                    <th>راننده</th>
                    <th>پلاک</th>
                    <th>تاریخ جمع‌آوری</th>
                    <th>اقلام جمع‌آوری شده</th>
                    <th>مقدار اقلام تحویلی</th>
                    <th>ارزش ریالی اقلام</th>
                    <th>شناسه پرداخت شهروند</th>
                    <th>سهم شهروند</th>
                </tr>
                </thead>
                @if($this->requests->count())
                    <tbody>
                    @foreach($this->requests as $request)
                        <tr>
                            <td>{{$request->id}}</td>
                            <td>{{$request->fava_id}}</td>
                            <td data-bs-toggle="tooltip" data-bs-title="{{ $request->address->address }}">
                                <i class="bx bx-map"></i>
                            </td>
                            <td>{{ $request->submit_phone ? 'تلفنی' : 'اپلیکیشن' }}</td>
                            <td>{{ \Carbon\Carbon::parse($request->end_deadline)->diffInhours(\Carbon\Carbon::parse($request->start_deadline)) <= 1 ? 'فوری' : 'زمان‌بندی‌شده' }}</td>
                            <td>{{ \Verta::instance($request->created_at)->format('Y/m/d H:i:s') }}</td>
                            <td>{{ \Verta::instance($request->start_deadline)->format('Y/m/d H:i:s') }}</td>
                            <td>{{ \Verta::instance($request->end_deadline)->format('Y/m/d H:i:s') }}</td>
                            <td>{{$request->status('label')}}</td>
                            @if($request->drivers->count())
                                <td>
                                    <a href="{{route('d.drivers.single',$request->drivers[0]->id)}}">{{$request->drivers[0]->user->name.' '.$request->drivers[0]->user->lastname}}</a>
                                </td>
                                <td>
                                    <div class="cr-plate">
                                        <span class="cr-id">
                                            <span class="font-size-12">ایران</span>
                                            <span>{{$request->drivers[0]->car->plaque_4}}</span>
                                        </span>
                                        <span class="cr-number">{{ $request->drivers[0]->car->plaque_3.' '.$request->drivers[0]->car->plaque_2.' '.$request->drivers[0]->car->plaque_1 }}</span>
                                        <span class="cr-flag">
                                            <img src="{{asset('/assets/img/iran.png')}}" alt="" class="img-fluid">
                                            <i>I.R.</i>
                                            <i>IRAN</i>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    {{ $request->drivers[0]->receives->count() ? \Verta::instance($request->drivers[0]->receives[0]->updated_at)->format('Y/m/d') : ''}}
                                    {{ $request->drivers[0]->receives->count() ? \Verta::instance($request->drivers[0]->receives[0]->updated_at)->format('H:i:s') : ''}}
                                </td>
                                <td>@foreach ($request->drivers[0]->receives as $receive){{ $receive->title }}({{ weightFormat($receive->weight).' ' }}) @endforeach</td>
                                <td>{{ weightFormat($request->drivers[0]->receives->pluck('weight')->sum()) }}</td>

                                <td class="number">
                                    @php
                                        $total_price = 0;
                                        foreach ($request->drivers[0]->receives as $receive){
                                            $total_price += App\Models\RecyclableHistory::whereDate('created_at', '<=', $request->start_deadline)->latest()->pluck($receive->fava_id)->first() * $receive->weight;
                                        }
                                    @endphp
                                    {{number_format($total_price)}}
                                    {{$total_price > 0 ? 'تومان' : ''}}
                                </td>
                                <td>{{ $request->drivers[0]->user_bank_code ?? '-' }}</td>
                                <td>
                                    {{number_format($request->total_amount)}}
                                    {{$request->total_amount > 0 ? 'تومان' : ''}}
                                </td>
                            @else
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                @endif
            </table>
        </div>
    </div>
    <div class="cr-card-footer">
        {{$this->requests->links(data: ['scrollTo' => '#paginated-requests'])}}
    </div>
</div>

