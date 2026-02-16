<div id="paginated-list">
    <div class="cr-card-body p-0">
        <div class="table-responsive text-center text-nowrap">
            <div wire:loading.class="cr-parent-spinner">
                {{spinner()}}
            </div>
            @if($this->drivers->count())
                <table class="cr-table table" >
                    <thead>
                    <tr>
                        <th>شناسه</th>
                        <th> نام و نام خانوادگی راننده</th>
                        <th>کل کارکرد</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($this->drivers as $driver)
                        <tr wire:key="{{$driver->user_id}}">
                            <td>{{$driver->user_id}}</td>
                            <td>{{ $driver->full_name }}</td>
                            <td>{{ $driver->total_hours_worked }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td>تعداد رانندگان در تاریخ :  {{$this->dateFrom}}  -  {{$this->dateTo}}</td>
                        <td></td>
                        <td> {{$this->drivers->count() }} </td>
                    </tr>
                    </tbody>
                </table>
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
        $(document).ready(function (){
            $('[data-bs-toggle="tooltip"]').tooltip()
        })
    </script>
    @endscript
</div>
