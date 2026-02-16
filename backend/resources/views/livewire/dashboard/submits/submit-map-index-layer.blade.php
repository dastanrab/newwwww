@php
    use App\Models\Submit;
    $gateDetails = Gate::allows('submit_map_index_detail',Submit::class);
 @endphp
<div>
    <livewire:dashboard.submits.submit-map-index-nav
        :instantCount="$this->instants->count()"
        :submit9Count="$this->submits9->count()"
        :submit11Count="$this->submits11->count()"
        :submit13Count="$this->submits13->count()"
        :submit15Count="$this->submits15->count()"
        :submit17Count="$this->submits17->count()"
        :activesCount="$this->actives->count()"
        :doneCount="$this->done->count()"
    />
    <div class="cr-card">
        <div class="cr-card-header">
            <div class="row align-items-baseline">
                <div class="col-md-5 col-12">
                    <div class="cr-title">
                        <div>
                          <strong>نقشه درخواست ها</strong><strong>({{$this->city_name}})</strong>
                        </div>
                    </div>
                </div>
                <div class="col-md-7 col-12">
                    <livewire:dashboard.submits.submit-map-index-filter/>
                </div>
            </div>
        </div>
        <div class="cr-card-body p-0">
            <div class="cr-map-section" id="cr-map-section" wire:ignore></div>
            @if($gateDetails)
                <livewire:dashboard.submits.submit-map-index-modal :submits="$this->instants" :name="'instant'"/>
                <livewire:dashboard.submits.submit-map-index-modal :submits="$this->submits9" :name="'submit9'"/>
                <livewire:dashboard.submits.submit-map-index-modal :submits="$this->submits11" :name="'submit11'"/>
                <livewire:dashboard.submits.submit-map-index-modal :submits="$this->submits13" :name="'submit13'"/>
                <livewire:dashboard.submits.submit-map-index-modal :submits="$this->submits15" :name="'submit15'"/>
                <livewire:dashboard.submits.submit-map-index-modal :submits="$this->submits17" :name="'submit17'"/>
                <livewire:dashboard.submits.submit-map-index-modal :submits="$this->actives" :name="'active'"/>
                <livewire:dashboard.submits.submit-map-index-modal :submits="$this->done" :name="'done'"/>
                <livewire:dashboard.submits.driver-map-index-modal :driverinfo="$this->driver_info" :name="'driverinfo'"/>
            @endif
            {{toast($errors)}}
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

        let polygons = {!! json_encode($this->polygons) !!};
        let lPolygon
        $.each( polygons, function( i, item ) {
            lPolygon = L.polygon(JSON.parse(item.polygon), {color: item.color}).addTo(map);
            lPolygon.bindTooltip(item.region, {permanent: true, direction:"center"})
        });

        /****************instants****************/
        var instantLayer = new L.LayerGroup().addTo(map);
        let instants = {!! json_encode($this->instants) !!};
        $.each( instants, function( i, item ) {
            let marker = L.marker(
                [
                    item.address.lat,
                    item.address.lon
                ],
                {
                    icon: L.IconMaterial.icon({
                        icon: '{{Submit::mapSettings()['instant']['icon']}}', // Name of Material icon
                        iconColor: '#fff', // Material icon color (could be rgba, hex, html name...)
                        markerColor: '{{Submit::mapSettings()['instant']['color']}}',  // Marker fill color
                        outlineColor: '{{Submit::mapSettings()['instant']['outlineColor']}}', // Marker outline color
                        outlineWidth: 3, // Marker outline width
                        iconSize: [25, 36] // Width and height of the icon
                    })
                }
            ).addTo(map).on('click', function(e) {
                $('#modal-instant-'+i).modal('show');
            });
            instantLayer.addLayer(marker);
        });
        if($('#submit-instant').attr('class') != 'active'){
            map.removeLayer(instantLayer);
        }
        $(document).on('click','#submit-instant',function (e){
            e.preventDefault();
            if($(this).attr('class') == 'active'){
                map.removeLayer(instantLayer);
                $(this).removeClass('active');
            }
            else{
                map.addLayer(instantLayer);
                $(this).addClass('active');
            }
        });
        /****************submits9****************/
        var submit9Layer = new L.LayerGroup().addTo(map);
        let submits9 = {!! json_encode($this->submits9) !!};
        $.each( submits9, function( i, item ) {
            let marker = L.marker(
                [
                    item.address.lat,
                    item.address.lon
                ],
                {
                    icon: L.IconMaterial.icon({
                        icon: '{{Submit::mapSettings()[9]['icon']}}',            // Name of Material icon
                        iconColor: '#fff',              // Material icon color (could be rgba, hex, html name...)
                        markerColor: '{{Submit::mapSettings()[9]['color']}}',  // Marker fill color
                        outlineColor: '{{Submit::mapSettings()[9]['outlineColor']}}',            // Marker outline color
                        outlineWidth: 3,                   // Marker outline width
                        iconSize: [25, 36]                 // Width and height of the icon
                    })
                }
            ).addTo(map).on('click', function(e) {
                $('#modal-submit9-'+i).modal('show');
            });
            submit9Layer.addLayer(marker);
        });
        if($('#submit-9').attr('class') != 'active'){
            map.removeLayer(submit9Layer);
        }
        $(document).on('click','#submit-9',function (e){
            e.preventDefault();
            if($(this).attr('class') == 'active'){
                map.removeLayer(submit9Layer);
                $(this).removeClass('active');
            }
            else{
                map.addLayer(submit9Layer);
                $(this).addClass('active');
            }
        });
        /****************submits11****************/
        var submit11Layer = new L.LayerGroup().addTo(map);
        let submits11 = {!! json_encode($this->submits11) !!};
        $.each( submits11, function( i, item ) {
            let marker = L.marker(
                [
                    item.address.lat,
                    item.address.lon
                ],
                {
                    icon: L.IconMaterial.icon({
                        icon: '{{Submit::mapSettings()[11]['icon']}}',            // Name of Material icon
                        iconColor: '#fff',              // Material icon color (could be rgba, hex, html name...)
                        markerColor: '{{Submit::mapSettings()[11]['color']}}',  // Marker fill color
                        outlineColor: '{{Submit::mapSettings()[11]['outlineColor']}}',            // Marker outline color
                        outlineWidth: 3,                   // Marker outline width
                        iconSize: [25, 36]                 // Width and height of the icon
                    })
                }
            ).addTo(map).on('click', function(e) {
                $('#modal-submit11-'+i).modal('show');
            });
            submit11Layer.addLayer(marker);
        });
        if($('#submit-11').attr('class') != 'active'){
            map.removeLayer(submit11Layer);
        }
        $(document).on('click','#submit-11',function (e){
            e.preventDefault();
            if($(this).attr('class') == 'active'){
                map.removeLayer(submit11Layer);
                $(this).removeClass('active');
            }
            else{
                map.addLayer(submit11Layer);
                $(this).addClass('active');
            }
        });
        /****************submits13****************/
        var submit13Layer = new L.LayerGroup().addTo(map);
        let submits13 = {!! json_encode($this->submits13) !!};
        $.each( submits13, function( i, item ) {
            let marker = L.marker(
                [
                    item.address.lat,
                    item.address.lon
                ],
                {
                    icon: L.IconMaterial.icon({
                        icon: '{{Submit::mapSettings()[13]['icon']}}',            // Name of Material icon
                        iconColor: '#fff',              // Material icon color (could be rgba, hex, html name...)
                        markerColor: '{{Submit::mapSettings()[13]['color']}}',  // Marker fill color
                        outlineColor: '{{Submit::mapSettings()[13]['outlineColor']}}',            // Marker outline color
                        outlineWidth: 3,                   // Marker outline width
                        iconSize: [25, 36]                 // Width and height of the icon
                    })
                }
            ).addTo(map).on('click', function(e) {
                $('#modal-submit13-'+i).modal('show');
            });
            submit13Layer.addLayer(marker);
        });
        if($('#submit-13').attr('class') != 'active'){
            map.removeLayer(submit13Layer);
        }
        $(document).on('click','#submit-13',function (e){
            e.preventDefault();
            if($(this).attr('class') == 'active'){
                map.removeLayer(submit13Layer);
                $(this).removeClass('active');
            }
            else{
                map.addLayer(submit13Layer);
                $(this).addClass('active');
            }
        });
        /****************submits15****************/
        var submit15Layer = new L.LayerGroup().addTo(map);
        let submits15 = {!! json_encode($this->submits15) !!};
        $.each( submits15, function( i, item ) {
            let marker = L.marker(
                [
                    item.address.lat,
                    item.address.lon
                ],
                {
                    icon: L.IconMaterial.icon({
                        icon: '{{Submit::mapSettings()[15]['icon']}}',            // Name of Material icon
                        iconColor: '#fff',              // Material icon color (could be rgba, hex, html name...)
                        markerColor: '{{Submit::mapSettings()[15]['color']}}',  // Marker fill color
                        outlineColor: '{{Submit::mapSettings()[15]['outlineColor']}}',            // Marker outline color
                        outlineWidth: 3,                   // Marker outline width
                        iconSize: [25, 36]                 // Width and height of the icon
                    })
                }
            ).addTo(map).on('click', function(e) {
                $('#modal-submit15-'+i).modal('show');
            });
            submit15Layer.addLayer(marker);
        });
        if($('#submit-15').attr('class') != 'active'){
            map.removeLayer(submit15Layer);
        }
        $(document).on('click','#submit-15',function (e){
            e.preventDefault();
            if($(this).attr('class') == 'active'){
                map.removeLayer(submit15Layer);
                $(this).removeClass('active');
            }
            else{
                map.addLayer(submit15Layer);
                $(this).addClass('active');
            }
        });
        /****************submits17****************/
        var submit17Layer = new L.LayerGroup().addTo(map);
        let submits17 = {!! json_encode($this->submits17) !!};
        $.each( submits17, function( i, item ) {
            let marker = L.marker(
                [
                    item.address.lat,
                    item.address.lon
                ],
                {
                    icon: L.IconMaterial.icon({
                        icon: '{{Submit::mapSettings()[17]['icon']}}',            // Name of Material icon
                        iconColor: '#fff',              // Material icon color (could be rgba, hex, html name...)
                        markerColor: '{{Submit::mapSettings()[17]['color']}}',  // Marker fill color
                        outlineColor: '{{Submit::mapSettings()[17]['outlineColor']}}',            // Marker outline color
                        outlineWidth: 3,                   // Marker outline width
                        iconSize: [25, 36]                 // Width and height of the icon
                    })
                }
            ).addTo(map).on('click', function(e) {
                $('#modal-submit17-'+i).modal('show');
            });
            submit17Layer.addLayer(marker);
        });
        if($('#submit-17').attr('class') != 'active'){
            map.removeLayer(submit17Layer);
        }
        $(document).on('click','#submit-17',function (e){
            e.preventDefault();
            if($(this).attr('class') == 'active'){
                map.removeLayer(submit17Layer);
                $(this).removeClass('active');
            }
            else{
                map.addLayer(submit17Layer);
                $(this).addClass('active');
            }
        });

        /****************actives****************/
        var activeLayer = new L.LayerGroup().addTo(map);
        let actives = {!! json_encode($this->actives) !!};
        $.each( actives, function( i, item ) {
            let marker = L.marker(
                [
                    item.address.lat,
                    item.address.lon
                ],
                {
                    icon: L.IconMaterial.icon({
                        icon: '{{Submit::mapSettings()['active']['icon']}}',            // Name of Material icon
                        iconColor: '#fff',              // Material icon color (could be rgba, hex, html name...)
                        markerColor: '{{Submit::mapSettings()['active']['color']}}',  // Marker fill color
                        outlineColor: '{{Submit::mapSettings()['active']['outlineColor']}}',            // Marker outline color
                        outlineWidth: 3,                   // Marker outline width
                        iconSize: [25, 36]                 // Width and height of the icon
                    })
                }
            ).addTo(map).on('click', function(e) {
                $('#modal-active-'+i).modal('show');
            });
            activeLayer.addLayer(marker);
        });
        if($('#submit-active').attr('class') != 'active'){
            map.removeLayer(activeLayer);
        }
        $(document).on('click','#submit-active',function (e){
            e.preventDefault();
            if($(this).attr('class') == 'active'){
                map.removeLayer(activeLayer);
                $(this).removeClass('active');
            }
            else{
                map.addLayer(activeLayer);
                $(this).addClass('active');
            }
        });

        /****************done****************/
        var doneLayer = new L.LayerGroup().addTo(map);
        let done = {!! json_encode($this->done) !!};
        let icon = '';
        $.each( done, function( i, item ) {
            console.log(item.count_submits);
            if(item.count_submits == 1 && item.flag == 0){
                icon = L.IconMaterial.icon({
                    icon: '{{Submit::mapSettings()['first']['icon']}}',            // Name of Material icon
                    iconColor: '#fff',              // Material icon color (could be rgba, hex, html name...)
                    markerColor: '{{Submit::mapSettings()['first']['color']}}',  // Marker fill color
                    outlineColor: '{{Submit::mapSettings()['first']['outlineColor']}}',            // Marker outline color
                    outlineWidth: 3,                   // Marker outline width
                    iconSize: [25, 36]                 // Width and height of the icon
                })
            }
            else{
                icon = L.IconMaterial.icon({
                    icon: '{{Submit::mapSettings()['done']['icon']}}',            // Name of Material icon
                    iconColor: '#fff',              // Material icon color (could be rgba, hex, html name...)
                    markerColor: '{{Submit::mapSettings()['done']['color']}}',  // Marker fill color
                    outlineColor: '{{Submit::mapSettings()['done']['outlineColor']}}',            // Marker outline color
                    outlineWidth: 3,                   // Marker outline width
                    iconSize: [25, 36]                 // Width and height of the icon
                })
            }

            let marker = L.marker(
                [
                    item.address.lat,
                    item.address.lon
                ],
                {
                    icon: icon
                }
            ).addTo(map).on('click', function(e) {
                $('#modal-done-'+i).modal('show');
            });
            doneLayer.addLayer(marker);
        });
        if($('#submit-done').attr('class') != 'active'){
            map.removeLayer(doneLayer);
        }
        $(document).on('click','#submit-done',function (e){
            e.preventDefault();
            if($(this).attr('class') == 'active'){
                map.removeLayer(doneLayer);
                $(this).removeClass('active');
            }
            else{
                map.addLayer(doneLayer);
                $(this).addClass('active');
            }
        });
        @if($this->driver_info)
        /****************driver****************/
        var driverLayer = new L.LayerGroup().addTo(map);
        let driver_info = {!! json_encode($this->driver_info) !!};
        let marker = L.marker(
            [
                driver_info.lat,
                driver_info.lon
            ],
            {
                icon: L.IconMaterial.icon({
                    icon: '{{Submit::mapSettings()['driver']['icon']}}', // Name of Material icon
                    iconColor: '#fff', // Material icon color (could be rgba, hex, html name...)
                    markerColor: '{{Submit::mapSettings()['driver']['color']}}',  // Marker fill color
                    outlineColor: '{{Submit::mapSettings()['driver']['outlineColor']}}', // Marker outline color
                    outlineWidth: 3, // Marker outline width
                    iconSize: [45, 66] // Width and height of the icon
                })
            }
        ).addTo(map).on('click', function(e) {
            $('#driverinfo').modal('show');
        });
        driverLayer.addLayer(marker);
        if ($('#submit-active').hasClass('active')) {
            map.addLayer(activeLayer);
        }
        @endif

            return map;
    }
    let runMap = buildMap();
</script>
@endscript
