<div>
    <div class="cr-overlay"></div>
    <div class="cr-layout-section">
        <livewire:dashboard.layouts.sidebar />
        <livewire:dashboard.layouts.navbar :$breadCrumb />
        <div class="cr-container-section">
            <livewire:dashboard.stats.stat-monthly-index-nav/>
            <div class="cr-card">
                <div class="cr-card-header">
                    <div class="cr-title">
                        <div>
                            <strong>آمار ماهانه</strong>
                            <button id="excel" class="btn btn-success">خروجی اکسل</button>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="cr-filter-section">
                        <div class="row">
                            <div class="col-lg-12 col-12">
                                <livewire:dashboard.stats.stat-monthly-index-filter/>
                            </div>
                        </div>
                    </div>
                </div>
                <livewire:dashboard.stats.stat-monthly-index-list lazy/>
            </div>
            <livewire:dashboard.layouts.footer/>
        </div>
    </div>
    @script
    <script>

        jQuery(document).ready(function ($){
            $(document).on('click','#excel',function (){
                let url = '{{route('d.export.stat.monthly_fin')}}'
                window.location.href = url;
            });
        })
    </script>
    @endscript
</div>
