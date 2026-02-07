<div id="paginated-list">
    <div class="cr-card-body p-0">
        <div class="table-responsive text-center text-nowrap">
            <div wire:loading.class="cr-parent-spinner">
                {{spinner()}}
            </div>
            @if($this->ranges)
                <table class="cr-table table" >
                    <thead>
                    <tr>
                        <th>تاریخ</th>
                        <th>صنفی</th>
                        <th>شهروندی</th>
                        <th>کل</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                    $i = 0;
                    $total=0;
                    $totalLegal=0;
                    $totalNotLegal=0;
                    @endphp
                    @foreach($this->ranges as $range)
                        @php
                        $key_legal = 'legal_'.$i;
                        $key_not_legal = 'not_legal_'.$i;
                        @endphp
                        <tr wire:key="{{$range}}">
                            <td>{{verta()->parse($range)->format('Y/m/d')}}</td>
                            <td>{{number_format($this->totals->$key_legal)}}</td>
                            <td>{{number_format($this->totals->$key_not_legal)}}</td>
                            @php
                            $total += ($this->totals->$key_legal+$this->totals->$key_not_legal);
                            $totalLegal += (int)$this->totals->$key_legal;
                            $totalNotLegal += (int)$this->totals->$key_not_legal;
                            @endphp
                            <td>{{number_format($this->totals->$key_legal+$this->totals->$key_not_legal)}}</td>
                        </tr>
                        @php
                            $i++
                        @endphp
                    @endforeach
                    <tr>
                        <td>کل</td>
                        <td>{{round($totalLegal)}}</td>
                        <td>{{round($totalNotLegal)}}</td>
                        <td>{{round($total)}}</td>
                    </tr>
                    <tr>
                        <td>میانگین</td>
                        <td>{{round($totalLegal/$i)}}</td>
                        <td>{{round($totalNotLegal/$i)}}</td>
                        <td>{{round($total/$i)}}</td>
                    </tr>
                    </tbody>
                </table>
            @else
                @include('livewire.dashboard.layouts.data-not-exists')
            @endif

        </div>
    </div>
    @script
    <script>
        $(document).ready(function (){
            $('[data-bs-toggle="tooltip"]').tooltip()
        })
    </script>
    @endscript
</div>
