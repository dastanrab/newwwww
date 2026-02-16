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
                        <th>عنوان</th>
                        <th>امتیاز</th>
                        <th>نام</th>
                        <th>کد</th>
                        <th>استفاده شده</th>
                        <th>تاریخ دریافت</th>
                        <th>عملیات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($this->offers as $item)
                        <tr wire:key="{{$item->id}}">
                            <td>{{$item->id}}</td>
                            <td>{{$item->title}}</td>
                            <td>{{number_format($item->score)}}</td>
                            <td>@if(isset($item->user_id)) {{$item->user->name.' '.$item->user->lastname}}@else انتصاب داده نشده @endif</td>
                            <td>{{$item->code}}</td>
                            <td>
                                <div class="cr-actions">
                                    <ul>
                                        @if($item->used)
                                            <li>
                                                <a href="#" data-bs-toggle="tooltip" title="استفاده شد" style="background-color: #27ae60">
                                                    <i class='bx bxs-check-square'></i>
                                                </a>
                                            </li>
                                        @else
                                            <li>
                                                <a href="#" data-bs-toggle="tooltip" title="استفاده نشده" style="background-color: #e74c3c">
                                                    <i class='bx bxs-minus-square'></i>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>

                            </td>
                            <td class="dir-ltr">{{verta()->instance($item->created_at)->format('Y/m/d H:i')}}</td>
                            <td>
                                <div class="cr-actions">
                                    <ul>
                                        @if(!$item->used)
                                            <li>
                                                <a href="#" class="used-btn" data-bs-toggle="tooltip" title="استفاده شد" data-offerid="{{$item->id}}">
                                                    <i class='bx bxs-check-circle' ></i>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
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
                icon: 'success',
                showCloseButton: true,
                showCancelButton: true,
                cancelButtonText: 'خیر',
                confirmButtonText: 'بله',
            }).then((result) => {
                console.log(result)
                if (result.isConfirmed) {
                    $wire.dispatch('used',{offerId : $(this).data('offerid')});
                }
            });
        });
    })
</script>
@endscript
