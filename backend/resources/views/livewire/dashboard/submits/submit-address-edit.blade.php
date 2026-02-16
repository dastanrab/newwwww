<div>
    <div class="cr-overlay"></div>
    <div class="cr-layout-section">
        <livewire:dashboard.layouts.sidebar />
        <livewire:dashboard.layouts.navbar :$breadCrumb />
        <div class="cr-container-section">
            <div class="cr-card mb-0">
                <div class="cr-card-header">
                    <div class="clearfix"></div>
                    <div class="cr-filter-section">
                        <div class="row">
                            <div class="col-12">
                                <form wire:submit.prevent="update">
                                    <div class="row">
                                        <div class="col-10">
                                            <div class="cr-text cr-md">
                                                <div class="search-datalist">
                                                    <input type="text" wire:model="full_address" placeholder="برای ذخیره کردن آدرس دقیق این قسمت را پر نماییددد" class="mb-2">
                                                    <input type="text" wire:model.live.debounce.1000ms="address_text" id="search-datalist" placeholder="جستجو آدرس">
                                                    <ul id="wizards-list">
                                                        @foreach($addresses as $item)
                                                            <li data-lat="{{$item['lat']}}" data-lng="{{$item['lng']}}" value="{{$item['lat']}}-{{$item['lng']}}">{{$item['address_text']}}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="cr-button">
                                                {{button()}}
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="cr-map-section" id="cr-map-section" wire:ignore></div>

            <livewire:dashboard.layouts.footer/>
        </div>
    </div>
    @script
    <script>

        let map;
        let mark;

        map = new L.Map("cr-map-section", {
            key: 'web.3dd03927cf104d018a4ce3cd6ec3c962',
            maptype: 'dreamy',
            center: [36.2966309, 59.6029849],
            zoom: 12,
            poi: true,
            traffic: false,
            zoomControl: false,
        });

        mark = L.marker([{{$address->lat}},{{$address->lon}}],15).addTo(map);
        map.setView([{{$address->lat}},{{$address->lon}}], 14);
        map.on('click', function(e){
            @this.set('lat',e.latlng.lat,true);
            @this.set('lng',e.latlng.lng,false);
            if(mark) {
                map.removeLayer(mark); // remove
            }
            mark = L.marker(e.latlng).addTo(map);
        });
        $(document).on('click','#wizards-list li',function (){

            $('#search-datalist').val($(this).text());
            @this.set('address',$(this).text(),false);
            @this.set('lat',$(this).data('lat'),false);
            @this.set('lng',$(this).data('lng'),false);
            console.log($(this).text(),$(this).data('lat'),$(this).data('lng'))
            if(mark) {
                map.removeLayer(mark); // remove
            }
            mark = L.marker([$(this).data('lat'),$(this).data('lng')],15).addTo(map);
            map.setView([$(this).data('lat'),$(this).data('lng')], 14);
        })

        $(window).click(function() {
            $('#wizards-list').addClass('cr-hidden');
        });

        $('#menucontainer').click(function(event){
            event.stopPropagation();
        });
    </script>
    @endscript

</div>

