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
                            <strong>آمار تعداد کاربران</strong>
                            <div class="cr-stats">
                                (
                                تعداد کل کاربران<strong>{{number_format($this->stats->legal+$this->stats->not_legal)}}،</strong>
                                تعداد اصناف<strong>{{number_format($this->stats->legal)}}،</strong>
                                تعداد شهروندان<strong>{{number_format($this->stats->not_legal)}}</strong>
                                )
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="cr-filter-section">
                        <div class="row">
                            <div class="col-lg-12 col-12">
                                <livewire:dashboard.stats.stat-total-user-index-filter/>
                            </div>
                        </div>
                    </div>
                </div>
                <livewire:dashboard.stats.stat-total-user-index-list lazy/>
            </div>
            <livewire:dashboard.layouts.footer/>
        </div>
    </div>
</div>
