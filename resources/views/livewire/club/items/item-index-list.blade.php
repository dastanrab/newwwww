<div id="paginated-list">
    <div class="cr-card-body p-0">
        <div class="table-responsive text-center text-nowrap">
            <div wire:loading.class="cr-parent-spinner">
                {{spinner()}}
            </div>
            @if($this->club->count())
                <table class="cr-table table" >
                    <thead>
                    <tr>
                        <th>شناسه</th>
                        <th>عنوان</th>
                        <th>زیرعنوان</th>
                        <th>امتیاز</th>
                        <th>دسته</th>
                        <th>وضعیت</th>
                        <th>تخفیف ها</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($this->club as $item)
                        <tr wire:key="{{$item->id}}">
                            <td>{{$item->id}}</td>
                            <td>{{$item->title}}</td>
                            <td>{{$item->sub_title}}</td>
                            <td>{{$item->score}}</td>
                            <td>{{$item->categories->count() ? $item->categories->first()->title : '-'}}</td>
                            <td>{{$item->status == 'active' ? 'فعال' : ''}}</td>
                            <td>
                                <a href="{{route('cl.item.edit',$item->id)}}" class="cr-edit">
                                    <i class="bx bxs-offer"></i>
                                </a>
                            </td>
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
        {{ $this->club->links(data: ['scrollTo' => '#paginated-list']) }}
    </div>
</div>
