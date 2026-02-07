<div>
    <div class="cr-overlay"></div>
    <div class="cr-layout-section">
        <livewire:dashboard.layouts.sidebar />
        <livewire:dashboard.layouts.navbar :$breadCrumb />
        <div class="cr-container-section">
            <livewire:dashboard.supervisor.supervisor-driver-index-nav/>

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
                    </div>
                    <div class="clearfix"></div>
                    <div class="cr-filter-section">
                        <div class="row">
                            <div class="col-lg-7 col-12">
                                <livewire:dashboard.supervisor.supervisor-driver-index-filter/>
                            </div>
                        </div>
                    </div>
                </div>
                <livewire:dashboard.supervisor.supervisor-driver-index-list lazy/>
            </div>
            <div class="clearfix"></div>
            <livewire:dashboard.layouts.footer/>
        </div>
    </div>
</div>
