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
                        <th>شناسه</th>
                        <th>نام و نام خانوادگی</th>
                        <th>درخواست های جمع آوری شده</th>
                        <th>مسافت طی شده (کیلومتر)</th>
                        <th>میانگین مسافت طی شده (کیلومتر)</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($this->drivers as $user)
                        @php
                            $sum = 0;
                            $avg = 0;
                            $distances = [];
                            for ($i = 0; $i < $user->drivers->count() - 1; $i++) {
                                $j = $i + 1;
                                $distances[] = getDistance($user->drivers[$i]->submit->address->lat,$user->drivers[$i]->submit->address->lon,$user->drivers[$j]->submit->address->lat,$user->drivers[$j]->submit->address->lon);
                            }
                            if($distances) {
                                $sum = array_sum($distances);
                                $avg = array_sum($distances)/$user->drivers->count();
                            }
                        @endphp
                        <tr wire:key="{{$user->id}}">
                            <td>{{$user->id}}</td>
                            <td>{{$user->name.' '.$user->lastname}}</td>
                            <td>{{number_format($user->drivers->count())}}</td>
                            <td>{{number_format($sum,3)}}</td>
                            <td>{{number_format($avg,3)}}</td>
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
        {{ $this->drivers->links(data: ['scrollTo' => '#paginated-list']) }}
    </div>
</div>
