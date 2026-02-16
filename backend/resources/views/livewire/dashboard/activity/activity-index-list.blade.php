<div id="paginated-list">
    <div class="cr-card-body p-0">
        <div class="table-responsive text-center text-nowrap">
            <div wire:loading>
                {{spinner()}}
            </div>
            @if($this->logs->count())
                <table class="cr-table table" >
                    <thead>
                    <tr>
                        <th>شناسه</th>
                        <th>نام و نام خانوادگی</th>
                        <th>نقش</th>
                        <th>موضوع</th>
                        <th>توضیحات</th>
                        <th>تغییرات</th>
                        <th>مسیر</th>
                        <th>آی پی</th>
                        <th>وضعیت</th>
                        <th>زمان</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($this->logs as $log)
                        <tr wire:key="{{$log->id}}">
                            <td>{{$log->id}}</td>
                            <td>{{$log->user->name.' '.$log->user->lastname}}</td>
                            <td>{{$log->user->getRole('label')}}</td>
                            <td>{{$log->subject_type}}</td>
                            <td>{{$log->description}}</td>
                            <td>{{json_encode($log->changes)}}</td>
                            <td>{{$log->path}}</td>
                            <td>{{$log->ip}}</td>
                            <td>
                                @if($log->result)
                                    <span class="badge bg-success">موفق</span>
                                @else
                                    <span class="badge bg-danger">ناموفق</span>
                                @endif
                            </td>
                            <td class="dir-ltr">{{$log->created_at}}</td>
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
        {{ $this->logs->links(data: ['scrollTo' => '#paginated-list']) }}
    </div>
</div>
