@php use App\Models\Warehouse; @endphp
<div id="paginated-list">
    <div class="cr-card-body p-0">
        <div class="table-responsive text-center text-nowrap">
            <div wire:loading.class="cr-parent-spinner">
                {{spinner()}}
            </div>
            <table class="cr-table table" >
                <thead>
                <tr>
                    <th>نام</th>
                    <th>جمع کل</th>
                    @foreach($this->recyclables as $recyclable)
                        <th>{{$recyclable}}</th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                @foreach($this->drivers as $driver)
                    <tr>
                        <td>{{$driver['user']->name.' '.$driver['user']->lastname}}</td>
                        <td>{{$driver['total']}}</td>
                        @isset($driver['receives'])
                            @foreach($driver['receives'] as $receive)
                                <td>{{$receive}}</td>
                            @endforeach
                        @endisset
                    </tr>
                @endforeach
                </tbody>
            </table>

        </div>
    </div>
    <div class="cr-card-footer">
    </div>
    @script
    <script>
    </script>
    @endscript
</div>
