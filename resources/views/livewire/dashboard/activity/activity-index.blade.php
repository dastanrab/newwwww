<div>

    <div class="cr-overlay"></div>
    <div class="cr-layout-section">
        <livewire:dashboard.layouts.sidebar />
        <livewire:dashboard.layouts.navbar :$breadCrumb />

        <div class="cr-container-section">
            <div class="cr-card">
                <div class="cr-card-header">
                    <div class="cr-title">
                        <div>
                            <strong>لیست فعالیت ها</strong>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="cr-filter-section">
                        <div class="row">
                            <div class="col-12">
                                <livewire:dashboard.activity.activity-index-filter/>
                            </div>
                        </div>
                    </div>
                </div>
                <livewire:dashboard.activity.activity-index-list lazy/>
            </div>
            <div class="clearfix"></div>
            <livewire:dashboard.layouts.footer/>
        </div>
    </div>
</div>
