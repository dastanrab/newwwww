<div id="paginated-list">
    <div class="cr-card-body p-0">
        <div class="table-responsive text-center text-nowrap">
            <div wire:loading.class="cr-parent-spinner">
                {{spinner()}}
            </div>
            @if($this->wastes->count())
                <table class="cr-table table" >
                    <thead>
                    <tr>
                        <th>عنوان پسماند</th>
                        <th>تناژ</th>
                        <th>مجموع پرداختی (تومان)</th>
                        <th>میانگین قیمت خرید (تومان)</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                        $total_price_all = 0
                    @endphp
                    @foreach($this->wastes as $waste)
                        <tr wire:key="{{$waste->id}}">
                            <td>{{ $waste->title }}</td>
                            <td>{{ $weights = $this->receives->where('fava_id', $waste->id)->pluck('weight')->sum() }}</td>
                            @php
                                $total_price = 0;
                                foreach ($this->receives->where('fava_id', $waste->id) as $r){
                                     $total_price += $r->weight * $r->price;
                                }
                            @endphp
                            <td>{{ number_format($total_price) }}</td>
                            @php
                                $total_price_all += $total_price;
                            @endphp
                            <td>{{ $weights == 0 ? 0 : number_format($total_price / $weights) }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td></td>
                        <td></td>
                        <td>{{number_format($total_price_all)}}</td>

                    </tr>
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

    </script>
    @endscript
</div>
