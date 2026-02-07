@php use App\Models\Percentage; @endphp
<div id="paginated-list">
    <div class="cr-card-body p-0">
        <div class="wrapper1" style="height: 20px; overflow-x: scroll; overflow-y:hidden;">
            <div class="top_scroll" >
            </div>
        </div>
        <div class="table-responsive text-center text-nowrap tableFixHead" id ="tableContainer">
            <div wire:loading.class="cr-parent-spinner">
                {{spinner()}}
            </div>
            <div class="float-end">جمع سهم شهروند:{{number_format($this->submits->totalAmount)}}</div>
            @if($this->submits->list->count())

                    <table class="cr-table table" >
                        <thead>
                        <tr>
                            <th>شناسه</th>
                            <th>شناسه فاوا</th>
                            @if(!$this->is_analyst)
                                <th>نام و نام خانوادگی</th>
                            @endif
                            <th>شهروندی</th>
                            <th>آدرس</th>
                            <th>شیوه درخواست</th>
                            <th>کیف پول</th>
                            <th>نوع درخواست</th>
                            <th>تاریخ ثبت درخواست</th>
                            <th>تاریخ درخواست جمع آوری</th>
                            <th>وضعیت</th>
                            <th>جزئیات لغو</th>
                            <th>راننده</th>
                            <th>تاریخ جمع آوری شده</th>
                            <th>تاریخ اولین پسماند ثبت شده</th>
                            <th>اقلام جمع آوری شده</th>
                            <th>مقدار اقلام تحویلی</th>
                            <th>ارزش اقلام</th>
                            <th>شناسه پرداخت شهروند</th>
                            <th>سهم شهروند</th>
                            <th>علت لغو</th>
                            @if(auth()->user()->isDeveloper())
                                <th>واریز پاداش راننده</th>
                            @endif

                        </tr>
                        </thead>
                        <tbody>
                        @foreach($this->submits->list as $submit)
                            <tr wire:key="{{$submit->id}}">
                                <td>{{$submit->id}}</td>
                                <td>
                                    <span>درخواست: {{$submit->fava_id ?? '-'}}</span>
                                    <br>
                                    <span>جمع آوری: {{$submit->driver->fava_id ?? '-'}}</span>
                                </td>
                                @if(!$this->is_analyst)
                                    <td>
                                        <a href="{{route('d.users.single',$submit->user->id)}}" class="cr-name" target="_blank">
                                            @if($submit->user->level == 2)
                                                {!! levelIcon() !!}
                                            @endif
                                            {{$submit->user->name || $submit->user->lastname ? $submit->user->name.' '.$submit->user->lastname : $submit->user->guild_title}}
                                        </a>
                                        <span>{{$submit->user->mobile}}</span>
                                    </td>
                                @endif
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
                                <td>
                                    @php
                                        if($submit->cashout_type == 'aap'){
                                            echo 'آپ';
                                        }
                                        elseif($submit->cashout_type == 'card'){
                                            echo 'کارت به کارت';
                                        }
                                        elseif($submit->cashout_type == 'bazist'){
                                            echo 'بازیست';
                                        }
                                    @endphp
                                </td>
                                <td class="dir-ltr">{{ $submit->is_instant == 1 ? 'فوری' : 'زمان‌بندی‌شده' }}</td>
                                <td class="dir-ltr">{{ \Verta::instance($submit->created_at)->format('Y/m/d H:i:s') }}</td>
                                <td class="dir-ltr">{{ \Verta::instance($submit->start_deadline)->format('Y/m/d H:i').'-'.\Verta::instance($submit->end_deadline)->format('H:i') }} </td>
                                <td>{{$submit->status('label')}}</td>
                                <td class="dir-ltr">
                                    {{$submit->canceller ? 'اپراتور: '.$submit->canceller->name.' '.$submit->canceller->lastname : ''}}
                                    <br>
                                    {{$submit->canceled_at ? verta()->instance($submit->canceled_at)->format('Y/m/d H:i:s') : ''}}
                                </td>
                                @if($submit->drivers->count())
                                    <td>
                                        <a href="{{route('d.drivers.single',$submit->driver->user_id)}}" target="_blank">
                                            {{$submit->driver->user->name.' '.$submit->driver->user->lastname}}
                                        </a>
                                        <br>
                                        <span>{{$submit->driver->user->mobile}}</span>
                                        <div class="clearfix"></div>
                                        <div class="cr-plate">
                                        <span class="cr-id">
                                            <span class="font-size-12">ایران</span>
                                            <span>{{$submit->drivers[0]->car->plaque_4}}</span>
                                        </span>
                                            <span class="cr-number">{{ $submit->drivers[0]->car->plaque_3.' '.$submit->drivers[0]->car->plaque_2.' '.$submit->drivers[0]->car->plaque_1 }}</span>
                                            <span class="cr-flag">
                                            <img src="{{asset('/assets/img/iran.png')}}" alt="" class="img-fluid">
                                            <i>I.R.</i>
                                            <i>IRAN</i>
                                        </span>
                                        </div>
                                    </td>
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
                                                                    <th>قیمت واحد سازمان (تومان)</th>
                                                                    <th>قیمت کل سازمان(تومان)</th>
                                                                </tr>
                                                                @php
                                                                    $totalWeight = 0;
                                                                    $totalPriceWeight = 0;
                                                                    $totalCompanyPriceWeight =0;
                                                                @endphp
                                                                @foreach ($submit->drivers[0]->receives as $receive)
                                                                    @php
                                                                        $totalWeight      += $receive->weight;
                                                                        $totalPriceWeight += $receive->price*$receive->weight;
                                                                        $totalCompanyPriceWeight += $receive->fava_price*$receive->weight
                                                                    @endphp
                                                                    <tr>
                                                                        <td>{{ $receive->title }}</td>
                                                                        <td>{{weightFormat($receive->weight)}}</td>
                                                                        <td>{{number_format($receive->price)}}</td>
                                                                        <td>{{number_format($receive->price*$receive->weight)}}</td>
                                                                        <td>{{number_format($receive->fava_price??0)}}</td>
                                                                        <td>{{number_format($receive->fava_price*$receive->weight)}}</td>
                                                                    </tr>
                                                                @endforeach
                                                                <tr>
                                                                    <td>کل</td>
                                                                    <td>{{{weightFormat($totalWeight)}}}</td>
                                                                    <td>-</td>
                                                                    <td>{{number_format($totalPriceWeight)}}</td>
                                                                    <td>-</td>
                                                                    <td>{{number_format($totalCompanyPriceWeight)}}</td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ weightFormat($submit->drivers[0]->receives->pluck('weight')->sum()) }}</td>

                                    {{--<td class="number">
                                        @php
                                            $total_price = 0;
                                            foreach ($submit->drivers[0]->receives as $receive){
                                                $total_price += App\Models\RecyclableHistory::whereDate('created_at', '<=', $submit->start_deadline)->latest()->pluck($receive->fava_id)->first() * $receive->weight;
                                            }
                                        @endphp
                                        {{number_format($total_price)}}
                                        {{$total_price > 0 ? 'تومان' : ''}}
                                    </td>--}}
                                    <td>
                                        {{number_format($submit->final_amount)}}
                                        {{$submit->final_amount > 0 ? 'تومان' : ''}}
                                    </td>
                                    <td>{{ $submit->drivers[0]->user_bank_code ?? '-' }}</td>
                                    <td>
                                        {{number_format(floor($submit->total_amount))}}
                                        {{$submit->total_amount > 0 ? 'تومان' : ''}}
                                    </td>
                                    {{--@php
                                    $receives = $submit->driver->receives;
                                    $requester = $submit->user;
                                    $realTotal = 0;
                                    foreach ($receives as $receive){
                                        $originalPrice = Percentage::where('recyclable_id', $receive->fava_id)->where('is_legal', $requester->legal)->where('weight', floor($receive->weight))->first();
                                        if ($originalPrice) {
                                            $price = $originalPrice->price;
                                        } else {
                                            $order = 'desc';
                                            if ($receive->weight <= 0) {
                                                $order = 'asc';
                                                $weight_request = $receive->weight;
                                            } else if ($receive->weight < 1) {
                                                $weight_request = 2;
                                            } else {
                                                $weight_request = $receive->weight;
                                            }
                                            $originalPrice = Percentage::where('recyclable_id', $receive->fava_id)->where('is_legal', $requester->legal)->orderBy('weight', $order)->where('weight', '<', $weight_request)->first()->price;
                                            $price = $originalPrice;
                                        }
                                        $realTotal += $price*$receive->weight;

                                    }

                                    @endphp

                                    <td>{{$submit->id}}</td>
                                    <td><span class="{{(int)$realTotal !== (int)$submit->total_amount ? 'text-danger' : ''}}">{{number_format($realTotal)}} تومان</span></td>--}}
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
                                <td>{{ $submit->cancel }}</td>
                                @if(auth()->user()->isDeveloper())
                                    <td>
                                        <button class="btn btn-sm btn-primary text-bg-primary" data-bs-toggle="tooltip" title="واریز پاداش راننده" wire:click="ReferralReward({{$submit->id}})">
                                            <i class='bx bx-wallet'></i>
                                        </button>
                                    </td>
                                @endif

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
        {{ $this->submits->list->links(data: ['scrollTo' => '#paginated-list']) }}
    </div>
    @script
    <script>
        $(document).ready(function (){
            $(".wrapper1").scroll(function(){
                $(".table-responsive")
                    .scrollLeft($(".wrapper1").scrollLeft());
            });
            $(".table-responsive").scroll(function(){
                $(".wrapper1")
                    .scrollLeft($(".table-responsive").scrollLeft());
            });
            let containerWidth = $(".table-responsive")[0].scrollWidth;
            $(".top_scroll").css("width", containerWidth + "px");
            $('[data-bs-toggle="tooltip"]').tooltip()
        })

    </script>
    @endscript
    {{toast($errors)}}
</div>
