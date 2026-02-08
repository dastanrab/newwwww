<div>
    <div class="cr-overlay"></div>
    <div class="cr-layout-section">
        <livewire:dashboard.layouts.sidebar />
        <livewire:dashboard.layouts.navbar :$breadCrumb />
        <div class="cr-container-section">
            <div class="cr-card">
                <div class="cr-card-header">
                    <div class="row align-items-baseline">
                        <div class="col-md-6 col-12">
                            <div class="cr-title">
                                <div>
                                    <strong>مختصات حضور و غیاب ناموفق </strong>
                                    <span>{{$failedRollcall->user->name.' '.$failedRollcall->user->lastname}}</span>
                                    <span>({{$failedRollcall->start_lat ? 'ورود' : 'خروج'}})</span>
                                    <span class="dir-ltr">{{verta()->instance($failedRollcall->created_at)->format('Y/m/d H:i:s')}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="cr-card-body p-0">
                    <div class="cr-map-section" id="cr-map-section" wire:ignore></div>
                </div>
            </div>
            <livewire:dashboard.layouts.footer/>
        </div>
    </div>
</div>

@script
<script>

    let buildMap = function(){
        let map = new L.Map("cr-map-section", {
            key: 'web.3dd03927cf104d018a4ce3cd6ec3c962',
            maptype: 'dreamy',
            center: [36.2966309, 59.6029849],
            zoom: 11,
            poi: true,
            traffic: false,
            zoomControl: false,
        });
        var colors = ['#EF5350', '#78909C', '#AB47BC', '#8D6E63', '#5C6BC0', '#FFA726', '#29B6F6', '#FFEE58', '#26C6DA', '#9CCC65', '#C2185B', '#616161', '#512DA8', '#E64A19', '#1976D2', '#FFA000', '#7D6E53', '#FF5354', '#A66354', '#FA5354', '#AF185B', '#8CFC65', '#186161', '#50909C', '#1D6FC0', '#812FA8', '#FC6B00', '#AF5050', '#0FA050',];

        let polygons = {!! json_encode($this->polygons) !!};
        let lPolygon
        $.each( polygons, function( i, item ) {
            lPolygon = L.polygon(JSON.parse(item.polygon), {color: colors[i]}).addTo(map);
            lPolygon.bindTooltip(item.region, {permanent: true, direction:"center"})
        });


        /****************submits8****************/

        var failedRollcallLayer = new L.LayerGroup().addTo(map);
        let lat = {{$failedRollcall->start_lat ? $failedRollcall->start_lat : $failedRollcall->end_lat }};
        let lon = {{$failedRollcall->start_lon ? $failedRollcall->start_lon : $failedRollcall->end_lon }};
        let marker = L.marker(
            [lat,lon]
        );
        failedRollcallLayer.addLayer(marker);

        return map;
    }
    let runMap = buildMap();
</script>
@endscript
