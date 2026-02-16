<div>
    <div class="cr-card-body p-0">
        <div class="table-responsive text-center text-nowrap">
            <div wire:loading>
                {{spinner()}}
            </div>
            @if($this->failed->count())
                <table class="cr-table table" >
                    <thead>
                    <tr>
                        <th>شناسه</th>
                        <th>حضور و غیاب</th>
                        <th>مختصات</th>
                        <th>تاریخ</th>
                        <th>جزئیات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($this->failed as $rollcall)
                        <tr wire:key="{{$rollcall->id}}">
                            <td>{{$rollcall->id}}</td>
                            <td>{{$rollcall->start_lat ? 'ورود' : 'خروج'}}</td>
                            <td>{{$rollcall->start_lat ? $rollcall->start_lat.','.$rollcall->start_lon : $rollcall->end_lat.','.$rollcall->end_lon}}</td>
                            <td class="dir-ltr">{{verta()->instance($rollcall->created_at)->format('Y/m/d H:i:s')}}</td>
                            <td><a href="{{route('d.drivers.failedRollcall',$rollcall->id)}}" target="_blank" class="cr-edit"><i class='bx bxs-compass' ></i></a></td>
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
        {{ $this->failed->links(data: ['scrollTo' => '#paginated-list']) }}
    </div>
    {{toast($errors)}}
</div>
