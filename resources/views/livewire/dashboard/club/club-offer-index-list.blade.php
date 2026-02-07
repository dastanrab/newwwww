
<div id="paginated-list">
    <div class="cr-card-body p-0">
        <div class="table-responsive text-center text-nowrap">
            <div wire:loading.class="cr-parent-spinner">
                {{spinner()}}
            </div>
            @if($this->offers->count())
                <table class="cr-table table" >
                    <thead>
                    <tr>
                        <th>شناسه</th>
                        <th>کاربر</th>
                        <th>کد</th>
                        <th>وضعیت</th>
                        <th>عنوان کد تخفیف</th>
                        <th>عنوان باشگاه</th>
                        <th>امتیاز</th>
                        <th>عملیات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($this->offers as $item)
                        <tr wire:key="{{$item->id}}">
                            <td>{{$item->id}}</td>
                            <td>@if(isset($item->user_id)) <a href="{{route('d.users.single',$item->user->id)}}">{{$item->user->name.' '.$item->user->lastname}}</a> @else انتصاب داده نشده @endif</td>
                            <td>{{$item->code}}</td>
                            <td>@if($item->staus == 0)استفاده نشده @else استفاده شده@endif </td>
                            <td>{{$item->title}}</td>
                            <td>{{$item->club->title}}</td>
                            <td>{{number_format($item->score)}}</td>
                            <td>
                                        <a href="#" style="color: red" class="used-btn" data-bs-toggle="tooltip" title="حذف شود" data-offerid="{{$item->id}}">
                                            <i class='bx bxs-x-circle' ></i>
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
        {{ $this->offers->links(data: ['scrollTo' => '#paginated-list']) }}
    </div>
</div>
@script
<script>
    $(document).ready(function (){

        $(document).on('click', '.used-btn', function(e) {
            Swal.fire({
                title: 'مهم',
                text: 'آیا مطمئن هستید؟',
                icon: 'danger',
                showCloseButton: true,
                showCancelButton: true,
                cancelButtonText: 'خیر',
                confirmButtonText: 'بله',
            }).then((result) => {
                console.log(result)
                if (result.isConfirmed) {
                    $wire.dispatch('delete',{offerId : $(this).data('offerid')});
                }
            });
        });
    })
</script>
@endscript
