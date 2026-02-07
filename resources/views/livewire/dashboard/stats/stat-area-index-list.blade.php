<div id="paginated-list">
    <div class="cr-card-body p-0">
        <div class="table-responsive text-center text-nowrap">
            <div wire:loading.class="cr-parent-spinner">
                {{spinner()}}
            </div>
            @if($this->items)
                <table class="cr-table table">
                    <thead>
                    <tr>
                        <th>منطقه</th>
                        <th>انجام شده (صنفی)</th>
                        <th>انجام نشده (غیرصنفی)</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                        $legalCount = 0;
                        $notLegalCount = 0;
                    @endphp
                    @foreach($this->items as $region => $item)
                        @php
                            $legalCount += $item['legal'];
                            $notLegalCount += $item['notLegal'];
                        @endphp
                        <tr wire:key="{{$region}}">
                            <td>{{$region}}</td>
                            <td>{{$item['legal']}}</td>
                            <td>{{$item['notLegal']}}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td>کل</td>
                        <td>{{$legalCount}}</td>
                        <td>{{$notLegalCount}}</td>
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
</div>
