<div class="cr-modal">
    <div class="modal fade" tabindex="-1" id="add-address-{{$user->id}}" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">افزودن آدرس</h5>
                    <button type="button" class="cr-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="bx bx-x"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="add">
                        <div class="row">
                            <div class="col-10">
                                <div class="cr-text cr-md">
                                    <div class="search-datalist">
                                        <input type="text" wire:model="address_text" placeholder="برای ثبت آدرس دقیق این قسمت را پر نمایید">
                                        <input type="text" wire:model.live.debounce.1000ms="address" id="search-datalist" placeholder="جستجو آدرس" class="mt-2">
                                        <ul id="wizards-list">
                                            @foreach($addresses as $item)
                                                <li data-lat="{{$item['lat']}}" data-lng="{{$item['lng']}}" value="{{$item['lat']}}-{{$item['lng']}}">{{$item['address']}}</li>
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
                    <div class="cr-map-section" id="cr-map-section" wire:ignore></div>
                </div>
            </div>
        </div>
    </div>
    {{toast($errors)}}
</div>
@script
<script>

    let myModal = document.getElementById('add-address-{{$user->id}}');
    let RunMap = false;
    let map;
    let mark;
    myModal.addEventListener('shown.bs.modal', function () {
        if(RunMap == false) {
            map = new L.Map("cr-map-section", {
                key: 'web.JzFjMmsUHWRA9y2ZGlhRiyBSrO1PUnbEXRXKeqEW',
                maptype: 'dreamy',
                center: [36.2966309, 59.6029849],
                zoom: 12,
                poi: true,
                traffic: false,
                zoomControl: false,
            });
            RunMap = true;
        }
        map.on('click', function(e){
            @this.set('lat',e.latlng.lat,true);
            @this.set('lng',e.latlng.lng,false);
            if(mark) {
                map.removeLayer(mark); // remove
            }
            mark = L.marker(e.latlng).addTo(map);
        });

    });

    $(document).on('click','#wizards-list li',function (){

        $('#search-datalist').val($(this).text());
        @this.set('address',$(this).text(),false);
        @this.set('lat',$(this).data('lat'),false);
        @this.set('lng',$(this).data('lng'),false);
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

    $wire.on('reload-address', (event) => {
        $('#add-address-{{$user->id}}').modal('hide');
    });
</script>
@endscript
