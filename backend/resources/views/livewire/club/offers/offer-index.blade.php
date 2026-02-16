<div>
    <div class="cr-overlay"></div>
    <div class="cr-layout-section">
        <livewire:club.layouts.sidebar />
        <livewire:club.layouts.navbar :$breadCrumb />
        <div class="cr-container-section">
            <div class="cr-card">
                <div class="cr-card-header">
                    <div class="cr-title">
                        <div>
                            <strong>کدهای تخفیف</strong>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="cr-filter-section">
                        <div class="row">
                            <div class="col-lg-12 col-12">
                                <livewire:club.offers.offer-index-filter/>
                            </div>
                        </div>
                    </div>
                </div>
                <livewire:club.offers.offer-index-list/>
            </div>
            <livewire:dashboard.layouts.footer/>
        </div>
    </div>
    {{toast($errors)}}
</div>
