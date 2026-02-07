<div>
    <div class="cr-overlay"></div>
    <div class="cr-layout-section">
        <livewire:dashboard.layouts.sidebar />
        <livewire:dashboard.layouts.navbar :$breadCrumb />
        <div class="cr-container-section">

            <div id="paginated-list">
                <div class="cr-card p-0">
                    <div class="table-responsive text-center text-nowrap">
                        <div wire:loading.class="cr-parent-spinner">
                            {{spinner()}}
                        </div>
                        <div class="row">
                            <div class="col-12 mb-2">
                                <table class="cr-table table">
                                    <thead>
                                    <tr>
                                        <th>پلاک</th>
                                        <th>نام</th>
                                        <th>تاریخ جمع آوری</th>
                                        <th>عملیات</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>
                                            <div class="cr-plate">
                                                <span class="cr-id"><span class="font-size-12">ایران</span><span>{{$user->car->plaque_4}}</span></span>
                                                <span class="cr-number">{{ $user->car->plaque_3.' '.$user->car->plaque_2.' '.$user->car->plaque_1 }}</span>
                                                <span class="cr-flag"><img src="{{asset('assets/img/iran.png')}}" alt="" class="img-fluid"><i>I.R.</i><i>IRAN</i></span>
                                            </div>
                                        </td>
                                        <td>{{$user->name.' '.$user->lastname}}</td>
                                        <td class="dir-ltr">{{ $this->collected->first() ?\Verta::instance($this->collected->first()->collected_at)->format('Y/m/d H:i') : '' }}</td>
                                        <td>
                                            <form wire:submit.prevent="store">
                                                <div class="cr-button">
                                                    {{button('ثبت')}}
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
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
                                        <td>{{weightFormat($weight[1]+(isset($weight[22])?$weight[22]:0))}}</td>
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
                                            $weightAll = $weight[1]+$weight[2]+$weight[3]+$weight[4]+$weight[5]+$weight[6]+$weight[7]+$weight[8]+$weight[9]+$weight[10]+$weight[11]+$weight[12]+$weight[13]+$weight[14]+$weight[15]+$weight[16]+$weight[17]+$weight[18]+$weight[19]+$weight[20]+$weight[21]+(isset($weight[22])?$weight[22]:0);
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
            <livewire:dashboard.layouts.footer/>
        </div>
    </div>
    {{toast($errors)}}
</div>


