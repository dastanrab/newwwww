<div>

    <div class="cr-overlay"></div>
    <div class="cr-layout-section">
        <livewire:dashboard.layouts.sidebar />
        <livewire:dashboard.layouts.navbar :$breadCrumb />

        <div class="cr-container-section">
            <livewire:dashboard.wallet.cashout-index-nav/>
            <div class="cr-card">
                <div class="cr-card-header">
                    <div class="cr-title">
                        <div>
                            <strong>{{$title}} ({{number_format($totalAmount)}} تومان)</strong>
                            <button id="excel" class="btn btn-success">خروجی اکسل</button>

                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="cr-filter-section">
                        <div class="row">
                            <div class="col-12">
                                <livewire:dashboard.wallet.cashout-index-filter/>
                            </div>
                        </div>
                    </div>
                </div>
                <livewire:dashboard.wallet.cashout-index-list lazy/>
            </div>
            <div class="clearfix"></div>
            <livewire:dashboard.layouts.footer/>
        </div>
    </div>
    @script
    <script>
        $(document).ready(function (){
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
                let url = '{{route('d.export.stat.cashout')}}?status={{$this->status}}'
                window.location.href = url;
            });
        })
    </script>
    @endscript
</div>
