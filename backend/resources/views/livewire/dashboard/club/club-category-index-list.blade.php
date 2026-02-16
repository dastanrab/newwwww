<div id="paginated-list">
    <div class="cr-card-body p-0">
        <div class="table-responsive text-center text-nowrap">
            <div wire:loading.class="cr-parent-spinner">
                {{spinner()}}
            </div>
            @if($this->categories->count())
                <table class="cr-table table" >
                    <thead>
                    <tr>
                        <th>شناسه</th>
                        <th>آیکون</th>
                        <th>عنوان</th>
                        @if(Gate::allows('club_category_edit',App\Models\Club::class))
                            <th>ویرایش</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($this->categories as $category)
                        <tr wire:key="{{$category->id}}">
                            <td>{{$category->id}}</td>
                            <td>
                                <img src="{{asset($category->icon)}}" width="25" alt="">
                            </td>
                            <td>{{$category->title}}</td>
                            @if(Gate::allows('club_category_edit',App\Models\Club::class))
                                <td>
                                    <a href="{{route('d.club.category.edit',$category->id)}}" class="cr-edit">
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

    </div>
</div>
