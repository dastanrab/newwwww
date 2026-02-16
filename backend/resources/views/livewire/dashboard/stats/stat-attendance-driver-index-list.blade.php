<div id="paginated-list">
    <div class="cr-card-body p-0" wire:poll.20000ms>
        <div class="table-responsive text-center text-nowrap">
            <div wire:loading.class="cr-parent-spinner">
                {{spinner()}}
            </div>
            <table class="cr-table table">
                <thead>
                <tr>
                    <th>شناسه</th>
                    <th>راننده</th>
                    <th>زمان شروع</th>
                    <th>زمان پایان</th>
                    <th>مدت زمان حاضر به کار</th>
                </tr>
                </thead>
                @if($this->attendances->count())
                    <tbody>
                    @foreach($this->attendances as $attendance)
                        <tr wire:key="{{$attendance->id}}">
                            <td>{{$attendance->user->id}}</td>
                            <td class="dir-ltr">
                                    <a href="{{route('d.stats.salary-driver-detail',$attendance->user->id)}}" class="cr-name">{{$attendance->user->name.' '.$attendance->user->lastname}}</a>
                            </td>
                            <td class="dir-ltr">{{ \Verta::instance($attendance->start_at)->format('Y/m/d H:i:s') }}</td>
                            <td class="dir-ltr">{{ isset($attendance->end_at)?\Verta::instance($attendance->end_at)->format('Y/m/d H:i:s'):'-' }}</td>
                            @php
                            $end=isset($attendance->end_at)?new \Carbon\Carbon($attendance->end_at):\Carbon\Carbon::now();
                            $start=new \Carbon\Carbon($attendance->start_at);
                            $diffForHumans = $start->diffForHumans($end);
                            @endphp
                            <td class="dir-ltr">{{$diffForHumans}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                @endif
            </table>
        </div>
    </div>
    <div class="cr-card-footer">
        {{$this->attendances->links(data: ['scrollTo' => '#paginated-list'])}}
    </div>
</div>
