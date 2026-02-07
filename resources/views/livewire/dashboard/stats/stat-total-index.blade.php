<div>
    <div class="cr-overlay"></div>
    <div class="cr-layout-section">
        <livewire:dashboard.layouts.sidebar />
        <livewire:dashboard.layouts.navbar :$breadCrumb />
        <div class="cr-container-section">
            <livewire:dashboard.stats.stat-total-index-nav/>
            <div class="cr-card">
                <div class="cr-card-header">
                    <div class="cr-title">
                        <div>
                            <strong>آمار کل</strong>
                        </div>
                    </div>
                </div>
                <livewire:dashboard.stats.stat-total-index-list lazy/>
            </div>
            <livewire:dashboard.layouts.footer/>
        </div>
    </div>
</div>
