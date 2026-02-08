<div>
    <div class="cr-overlay"></div>
    <div class="cr-layout-section">
        <livewire:dashboard.layouts.sidebar />
        <livewire:dashboard.layouts.navbar :$breadCrumb />


        <div class="cr-container-section">
            <div class="cr-card">
                <div class="cr-card-header">
                    <div class="row align-items-baseline">
                        <div class="col-md-5 col-12">
                            <div class="cr-title">
                                <div>
                                    <strong>ردیابی آنلاین</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7 col-12">
                            <div class="cr-filter-section">
                                <div class="cr-filters">
                                    <div class="cr-date">
                                        <label>تاریخ</label>
                                        <i class="bx bx-calendar-event"></i>
                                        <input type="text" autocomplete="off" wire:model="date" value="{{$date}}" id="date">
                                    </div>
                                    <div class="cr-select md" id="">
                                        <select name="" aria-controls="" class="select" id="user" wire:model="userId">
                                            <option value="">انتخاب راننده</option>
                                            @foreach($this->drivers as $driver)
                                                <option value="{{$driver->id}}">{{$driver->name.' '.$driver->lastname}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="cr-card-body p-0">
                    <div class="cr-map-section" id="cr-map-section"></div>
                </div>
                <div class="cr-card-footer p-0">
                </div>
            </div>
            <livewire:dashboard.layouts.footer/>
        </div>
    </div>
    @script
    <script>

        $(document).ready(function () {

            $(document).on('change','#user',function (){
                @this.set('userId', $(this).val(), true);
                //window.location.reload();
            });

            $('#date').persianDatepicker({
                calendar: {
                    persian: {
                        leapYearMode: 'astronomical'
                    },},
                defaultValue: '',
                observer: true,
                initialValueType: 'persian',
                format: 'YYYY/MM/DD',
                initialValue: false,
                autoClose: true,
                onSelect: function (unix) {
                    @this.
                    set('date', new persianDate(unix).format('YYYY/MM/DD'), true)
                    //window.location.reload();
                }
            });
        });

        if($('#cr-map-section').length) {
            map = new L.Map("cr-map-section", {
                key: 'web.3dd03927cf104d018a4ce3cd6ec3c962',
                maptype: 'dreamy',
                center: [36.2966309, 59.6029849],
                zoom: 12,
                poi: true,
                traffic: false,
                zoomControl: false,
            });

            @if($userId)
                let locations;
                let polyLinesLatLng = [];
                locations = {!! json_encode($this->locations) !!};
                $.each( locations, function( i, loc ) {
                    polyLinesLatLng[i] = [loc.lat, loc.long];
                });
                let polyline = L.polyline(polyLinesLatLng, {color: 'red'}).arrowheads({size: '5px'}).addTo(map);
                map.fitBounds(polyline.getBounds());

                let marker = L.marker(
                    [
                        locations[polyLinesLatLng.length-1].lat,
                        locations[polyLinesLatLng.length-1].long
                    ]/*,
                            {
                                icon: L.icon({
                                    iconUrl: '{{asset('assets/img/icons/person-red.png')}}',
                                    conSize: [40, 40]
                                })
                            }*/
                ).bindPopup('<div class="text-end"><strong>'+locations[polyLinesLatLng.length-1].name+' '+locations[polyLinesLatLng.length-1].lastname+'<br>'+'<span class="dir-ltr">آخرین اتصال: '+locations[polyLinesLatLng.length-1].date+'</span></strong></div>', {autoClose:false}).addTo(map).openPopup();

            @else
                let locations;
                let polyLinesLatLng;
                var locationLayer = new L.LayerGroup().addTo(map);
                locations = {!! json_encode($this->locations) !!};
                $.each( locations, function( i, loc ) {
                    console.log(loc)
                    let marker = L.marker(
                        [
                            loc.lat,
                            loc.long
                        ]/*,
                        {
                            icon: L.icon({
                                iconUrl: '{{asset('assets/img/icons/person-red.png')}}',
                                conSize: [40, 40]
                            })
                        }*/
                    ).bindPopup('<div class="text-end"><strong>'+loc.name+' '+loc.lastname+'<br>'+'<span class="dir-ltr">آخرین اتصال: '+loc.date+'</span></strong></div>', {autoClose:false}).addTo(map).openPopup();
                    locationLayer.addLayer(marker).openPopup();
                });
            @endif
        }

    </script>
    @endscript
</div>
