
<div>
    <div class="cr-overlay"></div>
    <div class="cr-layout-section">
        <livewire:dashboard.layouts.sidebar />
        <livewire:dashboard.layouts.navbar :$breadCrumb />
        <div class="cr-container-section">
            <livewire:dashboard.stats.stat-high-average-nav/>
            <div class="cr-card">
                <div class="cr-card-header">
                    <div class="cr-title">
                        <div>
                            <strong>بالای ۵۰ کیلو</strong>
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
                                           عنوان صنفی
                                        </td>
                                    <td>
                                        میانگین وزن
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
                                                {{$user->guild_title}}
                                            </td>
                                            <td>
                                                {{number_format($user->average_weight, 2, '.', '')}}
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
