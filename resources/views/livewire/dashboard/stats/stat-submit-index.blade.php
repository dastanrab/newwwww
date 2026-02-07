<div>
    <div class="cr-overlay"></div>
    <div class="cr-layout-section">
        <livewire:dashboard.layouts.sidebar />
        <livewire:dashboard.layouts.navbar :$breadCrumb />
        <div class="cr-container-section">
            <livewire:dashboard.stats.stat-submit-index-nav/>
            <div class="cr-card">
                <div class="cr-card-header">
                    <div class="cr-title">
                        <div>
                            <strong>آمار درخواست ها</strong>
                            <button id="excel" class="btn btn-success">خروجی اکسل</button>
                            @if(Gate::allows('stat_submit_division_excel',App\Livewire\Dashboard\Stats\StatSubmitIndex::class))
                                <button id="excel_plus" class="btn btn-primary">خروجی اکسل *</button>
                            @endif
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="cr-filter-section">
                        <div class="row">
                            <div class="col-lg-1 col-12">
                                <livewire:dashboard.layouts.table-row/>
                            </div>
                            <div class="col-lg-11 col-12">
                                <livewire:dashboard.stats.stat-submit-index-filter/>
                            </div>
                        </div>
                    </div>
                </div>
                <livewire:dashboard.stats.stat-submit-index-list lazy/>
            </div>
            <livewire:dashboard.layouts.footer/>
        </div>
    </div>
    {{-- {{route('d.export.statDaily')}}?dateFrom={{$dateFrom}}&dateTo={{$dateTo}} --}}
    {{$dateFrom}}
    @script
    <script>

        jQuery(document).ready(function ($){
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
                console.log(query)
                let url = '{{route('d.export.statDaily')}}?dateFrom='+query.dateFrom+'&dateTo='+query.dateTo+'&type='+query.type+'&driver_id='+query.driverId+'&search='+query.search;
                //console.log(url);
                window.location.href = url;
            });
            $(document).on('click','#excel_plus',function (){
                let query = getUrlVars();
                console.log(query)
                let url = '{{route('d.export.statDailyDivision')}}?dateFrom='+query.dateFrom+'&dateTo='+query.dateTo+'&type='+query.type+'&driver_id='+query.driverId+'&search='+query.search;
                //console.log(url);
                window.location.href = url;
            });
        })
    </script>
    @endscript
</div>
