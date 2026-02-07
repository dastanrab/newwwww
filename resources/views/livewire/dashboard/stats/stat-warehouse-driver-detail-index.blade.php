<div>
    <div class="cr-overlay"></div>
    <div class="cr-layout-section">
        <livewire:dashboard.layouts.sidebar />
        <livewire:dashboard.layouts.navbar :$breadCrumb />
        <div class="cr-container-section">
            <livewire:dashboard.stats.stat-warehouse-driver-detail-index-nav/>
            <div class="cr-card">
                <div class="cr-card-header">
                    <div class="cr-title">
                        <div>
                            <strong>آمار بار رانندگان</strong>
                            <button id="excel" class="btn btn-success">خروجی اکسل</button>

                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="cr-filter-section">
                        <div class="row">
                            <div class="col-lg-12 col-12">
                                <livewire:dashboard.stats.stat-warehouse-driver-detail-index-filter/>
                            </div>
                        </div>
                    </div>
                </div>
                <livewire:dashboard.stats.stat-warehouse-driver-detail-index-list lazy/>
            </div>
            <livewire:dashboard.layouts.footer/>
        </div>
    </div>
    <script>
        function getUrlVars()
        {
            var vars = [], hash;
            var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
            for(var i = 0; i < hashes.length; i++)
            {
                hash = hashes[i].split('=');
                vars.push(hash[0]);
                vars[hash[0]] = hash[1];
            }
            return vars;
        }
        $(document).on('click','#excel',function (){
            let query = getUrlVars();
            let url = '{{route('d.export.stat.warehouse_driver_detail')}}?date='+query.date;
            window.location.href = url;
        });
    </script>
</div>
