
<div>
    <link rel="stylesheet" href="{{asset('/assets/css/select2.min.css')}}">
    <script src="{{asset('/assets/js/select2.min.js')}}"></script>
    <div class="cr-overlay"></div>
    <div class="cr-layout-section">
        <livewire:dashboard.layouts.sidebar />
        <livewire:dashboard.layouts.navbar :$breadCrumb />
        <div class="cr-container-section">
            <livewire:dashboard.stats.stat-other-total-nav/>
            <div class="cr-card">
                <div class="cr-card-header">
                    <div class="cr-title">
                        <div>
                            <strong>آمار پویا</strong>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                                <livewire:dashboard.stats.stat-other-total-index-filter/>
                </div>
                <livewire:dashboard.stats.stat-other-total-index-list lazy/>
            </div>
            <livewire:dashboard.layouts.footer/>
        </div>
    </div>
</div>
