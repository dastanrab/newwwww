
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
                            <strong>  کاربران منتخب<i class='bx bxs-star cr-heartbeat' style='color:#ffdd03'></i>  </strong>
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
نام
                                    </td>
                                    <td>
                                        نام خانوادگی
                                    </td>
                                        <td>
                                          موبایل
                                        </td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($this->users as $user)
                                        <tr>
                                            <td>{{$user->id}}</td>
                                            <td>
                                                {{$user->name}}
                                            </td>
                                            <td>
                                                {{$user->lastname}}
                                            </td>
                                            <td>
                                                {{$user->mobile}}
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
                        {{ $this->users->links(data: ['scrollTo' => '#paginated-list']) }}
                    </div>
                </div>
            </div>
            <livewire:dashboard.layouts.footer/>
        </div>
    </div>
</div>
