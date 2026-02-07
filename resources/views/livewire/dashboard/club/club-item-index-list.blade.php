@php
    $gate = Gate::allows('club_edit',App\Models\Club::class)
@endphp
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
                        <th>کاربر</th>
                        <th>شماره همراه</th>
                        <th>عنوان</th>
                        <th>زیرعنوان</th>
                        <th>امتیاز</th>
                        <th>وضعیت</th>
                        <td>نوع تخفیف</td>
                        <th>دسته</th>
                        @if($gate)
                            <th>ویرایش</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($this->club as $item)
                        <tr wire:key="{{$item->id}}">
                            <td>{{$item->id}}</td>
                            <td>{{$item->user->name.' '.$item->user->lastname}}</td>
                            <td>{{$item->user->mobile}}</td>
                            <td>{{$item->title}}</td>
                            <td>{{$item->sub_title}}</td>
                            <td>{{$item->score}}</td>
                            @if ($item->status == 'active')
                                <td data-bs-toggle="tooltip" title="فعال ">
                                    <div wire:click="update_status({{$item->id}},0)" class="cr-online">
                                        <i ></i>
                                    </div>
                                </td>
                            @else
                                <td data-bs-toggle="tooltip" title="غیرفعال">
                                    <div wire:click="update_status({{$item->id}},1)" class="cr-deactivate">
                                        <i ></i>
                                    </div>
                                </td>
                            @endif
                            <td>{{$item->discount_type == 1 ? 'درصدی' : 'ریالی'}}</td>
                            <td>{{$item->categories->count() ? $item->categories->first()->title : '-'}}</td>
                            @if($gate)
                                <td>
                                    <a href="{{route('d.club.edit',$item->id)}}" class="cr-edit">
                                        <i class="bx bxs-message-square-edit"></i>
                                    </a>
                                </td>
                            @endif
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
    {{toast($errors)}}
</div>
