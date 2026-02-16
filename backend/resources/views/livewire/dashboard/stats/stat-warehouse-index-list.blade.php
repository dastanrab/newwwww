<div id="paginated-list">
    <div class="cr-card-body p-0">
        <div class="table-responsive text-center text-nowrap">
            <div wire:loading.class="cr-parent-spinner">
                {{spinner()}}
            </div>
            @if($this->warehouses->count())
                <table class="cr-table table" >
                    <thead>
                    <tr>
                        <th>شناسه</th>
                        <th>راننده</th>
                        <th>شماره قبض</th>
                        <th>تاریخ</th>
                        <th>ساعت</th>
                        <th>جمع کل</th>
                        <th>کسری / اضافی بار</th>
                        @foreach ($this->recyclables as $recyclable)
                            <th>{{ $recyclable->title }}</th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($this->warehouses as $warehouse)
                        <tr wire:key="{{$warehouse->id}}">
                            <td>{{$warehouse->id}}</td>
                            <td><a href="{{route('d.drivers.single',$warehouse->car->user->id)}}">{{ $warehouse->car->user->name }} {{ $warehouse->car->user->lastname }}</a></td>
                            <td>{{ $warehouse->bascule_bill_number }}</td>
                            <td>{{ \Verta::instance($warehouse->received_at)->format('Y/m/d') }}</td>
                            <td>{{ \Verta::instance($warehouse->received_at)->format('H:i:s') }}</td>
                            <td>{{ $total_warehouse = $warehouse->warehouseItem->pluck('weight')->sum() }}</td>
                            @php
                                $total_weights = \App\Models\Driver::where('user_id', $warehouse->car->user_id)->where('status', 3)->whereDate('collected_at', \Carbon\Carbon::parse($warehouse->received_at))->pluck('weights')->sum();
                            @endphp
                            <td>
                                @if ($total_warehouse == $total_weights)
                                    0
                                @elseif ($total_weights - $total_warehouse > 0)
                                    <span class="dir-ltr text-success">(+{{ round($total_weights - $total_warehouse, 2) }})</span>
                                @else
                                    <span class="dir-ltr text-danger">({{ round($total_weights - $total_warehouse, 2) }})</span>
                                @endif
                            </td>
                            @foreach ($this->recyclables as $recyclable)
                                <td>
                                    @foreach ($warehouse->warehouseItem as $warehouseItem)
                                        @if ($recyclable->title == $warehouseItem->title)
                                            @php
                                                $sum_driver = 0;
                                                $drivers = \App\Models\Driver::where('user_id', $warehouse->car->user_id)->where('status', 3)->whereDate('collected_at', \Carbon\Carbon::parse($warehouse->received_at))->with('receives')->get();
                                                foreach ($drivers as $driver){
                                                    $sum_driver += $driver->receives->where('title', $recyclable->title)->pluck('weight')->sum();
                                                }
                                            @endphp
                                            @if ($warehouseItem->weight == $sum_driver)
                                                {{ $warehouseItem->weight }}
                                            @else
                                                {{ $warehouseItem->weight }}
                                                @if ($sum_driver - $warehouseItem->weight > 0)
                                                    <span class="dir-ltr text-success">(+{{ round($sum_driver - $warehouseItem->weight, 2) }})</span>
                                                @else
                                                    <span class="dir-ltr text-danger">({{ round($sum_driver - $warehouseItem->weight, 2) }})</span>
                                                @endif
                                            @endif
                                            @break
                                        @endif
                                    @endforeach
                                </td>
                            @endforeach
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
        {{ $this->warehouses->links(data: ['scrollTo' => '#paginated-list']) }}
    </div>
    @script
    <script>
        $(document).ready(function (){
            $('[data-bs-toggle="tooltip"]').tooltip()
        })
    </script>
    @endscript
</div>
