<div>
    <div class="cr-overlay"></div>
    <div class="cr-layout-section">
        <livewire:dashboard.layouts.sidebar />
        <livewire:dashboard.layouts.navbar :$breadCrumb />
        <div class="cr-container-section">
            <livewire:dashboard.stats.stat-driver-performance-nav/>
            <div class="cr-card">
                <div class="cr-card-header">
                    <div class="cr-title">
                        <div>
                            <strong>آمار کارکرد رانندگان</strong>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="cr-filter-section">
                        <div class="row">
                            <div class="col-lg-12 col-12">
                                <livewire:dashboard.stats.stat-driver-performance-index-filter/>
                            </div>
                        </div>
                    </div>
                </div>
                <livewire:dashboard.stats.stat-driver-performance-index-list lazy/>
            </div>
            <livewire:dashboard.layouts.footer/>
        </div>
    </div>
</div>
