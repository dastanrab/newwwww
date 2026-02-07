<div>
    <div class="cr-overlay"></div>
    <div class="cr-layout-section">
        <livewire:dashboard.layouts.sidebar />
        <livewire:dashboard.layouts.navbar :$breadCrumb />
        <div class="cr-container-section">
            <livewire:dashboard.drivers.driver-index-nav/>

            <div class="cr-card">
                <div class="cr-card-header">
                    <div class="cr-title">
                        <div>
                            <strong>لیست رانندگان</strong>
                            <div class="cr-stats">
                                (حاضر امروز <strong>{{$presentTodayCount}}</strong>
                                حاضر فعلی<strong>{{$currentPresentCount}}</strong>
                                غایب<strong>{{$absentCount}}</strong>)
                            </div>
                        </div>
                        @if(Gate::allows('user_driver_create',App\Models\User::class))
                            <div class="cr-actions">
                                <a class="cr-action cr-primary" href="{{route('d.drivers.create')}}" >
                                    <span> افزودن راننده</span>
                                    <i class="bx bx-plus"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                    <div class="clearfix"></div>
                    <div class="cr-filter-section">
                        <div class="row">
                            <div class="col-lg-5 col-12">
                                <livewire:dashboard.layouts.table-row/>
                            </div>
                            <div class="col-lg-7 col-12">
                                <livewire:dashboard.drivers.driver-index-filter/>
                            </div>
                        </div>
                    </div>
                </div>
                <livewire:dashboard.drivers.driver-index-list lazy/>
            </div>
            <div class="clearfix"></div>
            <livewire:dashboard.layouts.footer/>
        </div>
    </div>
</div>
