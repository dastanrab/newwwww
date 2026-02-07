@php
    use App\Models\User;use Hekmatinasser\Verta\Verta;
    if($date){
        $date = toGregorian($date,'/','-',false);
    }
    else{
        $date = date('Y-m-d');
    }
    $gate = Gate::allows('stat_warehouse_driver_index_create',App\Livewire\Dashboard\Stats\StatSubmitIndex::class);
@endphp
<div id="paginated-list">
    <div class="cr-card-body p-0">
        <div class="table-responsive text-center text-nowrap">
            <div wire:loading.class="cr-parent-spinner">
                {{spinner()}}
            </div>
            @if($this->drivers->count())
                <table class="cr-table table">
                    <thead>
                    <tr>
                        <th>وضعیت</th>
                        <th>نام و نام خانوادگی</th>
                        <th>جمع بار فعلی</th>
                        <th>جمع بار {{Verta::instance($date)->format('Y/m/d')}}</th>
                        <th>انبار آزادی (<span id="warehouse_1"></span>)</th>
                        <th>انبار میامی (<span id="warehouse_2"></span>)</th>
                        <th>جمع آوری شده</th>
                        <th>نسبت حضور/درخواست</th>
                        <th>نسبت جمع آوری/درخواست</th>
                        <th>آخرین جمع آوری</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php $row=0; $count = 0;
                    @endphp
                    @foreach($this->drivers as $user)
                        @php
                            // $driver = App\Models\Driver::where('user_id', $user->id)->where('status', 3)->orderBy('collected_at', 'desc')->first();
                             $driver = $drivers_latest_collect_date->where('user_id', $user->id)->first();
                             $drivers = $user->drivers;
                             if ($driver){
                                 $driver_diff_time = Carbon\Carbon::parse($driver->collected_at)->diffInMinutes();
                             }
                             else{
                                 $driver_diff_time = 0;
                             }
                             //$weight_today = $drivers->whereBetween('collected_at', [$date.' 00:00:00', $date.' 23:59:59'])->pluck('weights')->sum();
                             $weight_today = $drivers_weights->where('user_id',$user->id)->first()->weight??0;
                        @endphp
                        <tr wire:key="{{$user->id}}">
                            <td>
                                <div class="cr-circle
                                 @if ($driver)
                                    @if($driver_diff_time > 30 && $weight_today != 0)
                                       cr-yellow
                                    @endif
                                @endif
                                "></div>
                            </td>
                            <td class="cr-name cursor-pointer" data-bs-toggle="modal" data-bs-target="#warehouse-{{$user->id}}">
                                @if($gate)
                                    @php
                                        $queryString = $date !== now()->format('Y-m-d') ? "?date=".Verta::instance($date)->format('Y-m-d') : '';
                                    @endphp
                                    <a href="{{route('d.stats.warehouse-driver.submit',$user->id).$queryString}}">{{$user->name || $user->lastname ? $user->name.' '.$user->lastname : '-'}}</a>
                                @else
                                    {{$user->name || $user->lastname ? $user->name.' '.$user->lastname : '-'}}
                                @endif
                            </td>
                            @php
                                $weight = 0;
//                                 $today_latest_warehouse_date=$user->warehouseDailies->last();
//                                $collect_date=isset($today_latest_warehouse_date)?$today_latest_warehouse_date->created_at:date('Y-m-d');
//                                foreach ($drivers->where('collected_at', '>', \Carbon\Carbon::parse($collect_date)) as $item){
//                                    $weight += $item->weights;
//                                }
                            $weight=$drivers_current_weight->where('user_id',$user->id)->first()->weight??0
                            @endphp
                            <td>{{$weight ? weightFormat($weight) : '0'}}</td>
                            <td>{{$weight_today ? weightFormat($weight_today) : '0'}}</td>
                            @php
                                $warehouse_1_weight = 0;
                                if ($latest = $user->warehouseDailies()->whereIn('operator_id', User::azadiId())->whereDate('created_at', $date)->latest()->first()){
                                    if($first_collected = App\Models\WarehouseDaily::where('user_id',$user->id)->whereDate('created_at',$date)->first()){
                                        $collected_before = App\Models\WarehouseDaily::where('user_id',$user->id)->where('created_at','<', $first_collected->created_at)->latest()->first();
                                        if($collected_before){

                                            foreach ($drivers->whereBetween('collected_at', [$collected_before->created_at,$latest->created_at]) as $item){
                                                $warehouse_1_weight += $item->weights;
                                            }
                                        }
                                    }
                                }
                            @endphp
                            <td id="w1-{{$row}}"
                                data-weight="{{$warehouse_1_weight}}">{{ $warehouse_1_weight ? weightFormat($warehouse_1_weight) : 0}}</td>
                            @php
                                $warehouse_2_weight = 0;
                                if ($latest = $user->warehouseDailies()->where('operator_id', User::mayameyId())->whereDate('created_at', $date)->latest()->first()){
                                    if($first_collected = App\Models\WarehouseDaily::where('user_id',$user->id)->whereDate('created_at', $date)->first()){
                                        $collected_before = App\Models\WarehouseDaily::where('user_id',$user->id)->where('created_at','<', $first_collected->created_at)->latest()->first();
                                        if($collected_before){
                                            foreach ($drivers->whereBetween('collected_at', [$collected_before->created_at,$latest->created_at]) as $item){
                                                $warehouse_2_weight += $item->weights;
                                            }
                                        }
                                    }
                                }
                            @endphp
                            <td id="w2-{{$row}}"
                                data-weight="{{$warehouse_2_weight}}">{{ $warehouse_2_weight ? weightFormat($warehouse_2_weight) : 0}}</td>
                            @php
                                $collected = $drivers->where('status', 3)->count();
                            //dump($collected)
                            @endphp
                            <td>{{ number_format($collected) }}</td>
                            @php
                                $rollcall_diff = 0;
//                                App\Models\Rollcall::where('user_id', $user->id)->whereDate('start_at', $date)->get()
                                if ($rollcalls=$user->rollcalls){
                                    if ($rollcalls->count() == 1){
                                        $rollcall_diff = Carbon\Carbon::parse($rollcalls[0]->start_at)->diffInMinutes($rollcalls[0]->end_at);
                                    }
                                    else{
                                        foreach ($rollcalls as $rollcall){
                                            $rollcall_diff += Carbon\Carbon::parse($rollcall->start_at)->diffInMinutes($rollcall->end_at);
                                        }
                                    }
                                }
                            @endphp
                            <td>{{ number_format($collected ? round($rollcall_diff / $collected) : 0).' دقیقه' }}</td>
                            <td>{{ number_format($collected ? round($weight_today / $collected, 3) : 0)}}</td>
                            <td>{{ $driver && $weight_today ? number_format($driver_diff_time).' دقیقه پیش' : '-' }}</td>
                        </tr>
                        @php($row += 1)
                    @endforeach
                    </tbody>
                </table>
                {{$count}}
            @else
                @include('livewire.dashboard.layouts.data-not-exists')
            @endif

        </div>
    </div>
    <div class="cr-card-footer">
        {{ $this->drivers->links(data: ['scrollTo' => '#paginated-list']) }}
    </div>

    @script
    <script>
        jQuery(document).ready(function ($){
            let warehouse_1_weights = 0;
            let warehouse_2_weights = 0;
            for (let index = 0; index < {{ $this->drivers->count() }}; index++) {
                warehouse_1_weights += Number($('#w1-' + index).data('weight'));
                warehouse_2_weights += Number($('#w2-' + index).data('weight'));
            }
            $('#warehouse_1').text(warehouse_1_weights);
            $('#warehouse_2').text(warehouse_2_weights);
        });
    </script>
    @endscript
</div>
