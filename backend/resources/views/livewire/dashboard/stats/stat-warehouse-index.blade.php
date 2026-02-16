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
                            <strong>آمار کلی انبار</strong>
                        </div>
                        @if(Gate::allows('stat_warehouse_create',App\Livewire\Dashboard\Stats\StatSubmitIndex::class))
                        <div class="cr-actions">
                            <a class="cr-action cr-primary" href="{{route('d.stats.warehouse.create')}}">
                                <span>افزودن قبض</span>
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
                                <livewire:dashboard.stats.stat-warehouse-index-filter/>
                            </div>
                        </div>
                    </div>
                </div>
                <livewire:dashboard.stats.stat-warehouse-index-list lazy/>
            </div>
            <div class="clearfix"></div>
            <livewire:dashboard.layouts.footer/>
        </div>
    </div>
</div>
