
<div>
    <div class="cr-overlay"></div>
    <div class="cr-layout-section">
        <livewire:dashboard.layouts.sidebar />
        <livewire:dashboard.layouts.navbar :$breadCrumb />
        <div class="cr-container-section">
            <livewire:dashboard.stats.stat-top-users-nav/>
            <div class="cr-card">
                <div class="cr-card-header">
                    <div class="cr-title">
                        <div>
                            <strong>  آینکس های در انتظار<i class='bx bxs-star cr-heartbeat' style='color:#ffdd03'></i>  </strong>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="cr-filter-section">
                        <div class="row">
                            <div class="col-lg-12 col-12">

                            </div>
                        </div>
                    </div>
                </div>
                <div id="paginated-list">
                    <div class="cr-card-body p-0">
                        <div class="table-responsive text-center text-nowrap">
                            <div wire:loading.class="cr-parent-spinner">
                                {{spinner()}}
                            </div>
                            @if($this->users)
                                <table class="cr-table table">
                                    <thead>
                                    <tr>
                                        <td>شناسه</td>
                                    <td>
شناسه سفارش
                                    </td>
                                    <td>
                                        نام و نام خانوادگی
                                    </td>
                                        <td>
                                          موبایل
                                        </td>
                                        <td>
                                            مبلغ
                                        </td>
                                        <td>
                                            اوپراتور
                                        </td>
                                        <td>
                                            تاریخ
                                        </td>
                                        <td>
                                            تعیین وضعیت
                                        </td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($this->users as $user)
                                        <tr>
                                            <td>{{$user->id}}</td>
                                            <td>
                                                {{$user->order_id??'-'}}
                                            </td>
                                            <td>
                                                {{$user->user->name.' '.$user->user->lastname}}
                                            </td>
                                            <td>
                                                {{$user->mobile}}
                                            </td>
                                            <td>
                                                {{$user->amount}}
                                            </td>
                                            <td>
                                                {{$user->operator}}
                                            </td>
                                            <td>
                                                {{\Verta::instance($user->created_at)->format('Y/m/d H:i')}}
                                            </td>
                                            <td>
{{--                                                <button class="btn btn-sm btn-success text-bg-success" title="تغییر وضعیت به پرداخت شده" data-bs-toggle="modal" data-bs-target="#inax-{{$user->id}}">--}}
{{--                                                    موفق--}}
{{--                                                </button>--}}
                                                <button class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="تغیین وضعیت " wire:click="check_inax({{$user->id}})" >
                                                    تعیین وضعیت
                                                </button>
                                            </td>
                                        </tr>
                                        <div class="cr-modal">
                                            <div class="modal fade" tabindex="-1"
                                                 id="inax-{{$user->id}}" wire:ignore>
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">تعیین وضعیت</h5>
                                                            <button type="button" class="cr-close"
                                                                    data-bs-dismiss="modal" aria-label="Close">
                                                                <i class="bx bx-x"></i>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form wire:submit.prevent="save('{{$user->id}}')">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="cr-text cr-icon cr-md mb-3">
                                                                        <label for="mobile">شناسه</label>
                                                                        <i class="bx bx-user-circle"></i>
                                                                        <input wire:model="id" type="text" disabled value="{{$user->id}}" placeholder="{{$user->id}}">
                                                                    </div>
                                                                </div>
                                                                    <div class="col-12">
                                                                        <div class="cr-text cr-icon cr-md mb-3">
                                                                            <label for="trans_id">شماره تراکنش</label>
                                                                            <i class="bx bx-user-circle"></i>
                                                                            <input wire:model="trans_id" type="text" id=trans_id" placeholder="شماره تراکنش را وارد نمایید">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <div class="cr-text cr-icon cr-md mb-3">
                                                                            <label for="code">شماره مرجع</label>
                                                                            <i class="bx bx-user-circle"></i>
                                                                            <input wire:model="ref_id" type="text" id="ref_id" placeholder="شماره مرجع را وارد نمایید">
                                                                        </div>
                                                                    </div>
                                                            </div>
                                                                <div class="cr-button">
                                                                    {{button('ثبت')}}
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    </tbody>
                                </table>
                                <!-- Modal -->
                            @else
                                @include('livewire.dashboard.layouts.data-not-exists')
                            @endif

                        </div>
                    </div>
                    <div class="cr-card-footer">
                        {{ $this->users->links(data: ['scrollTo' => '#paginated-list']) }}
                    </div>
                </div>
            </div>
            <livewire:dashboard.layouts.footer/>
        </div>
    </div>
    @script
    <script>

        $wire.on('remove-modal', (event) => {
            $('body').removeClass('modal-open');
            $('.modal-backdrop').remove();
            $('.modal').modal('hide');
        });

    </script>
    @endscript
    {{toast($errors)}}

</div>
