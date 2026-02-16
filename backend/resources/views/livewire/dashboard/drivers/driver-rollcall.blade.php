<div>
    <div class="cr-overlay"></div>
    <div class="cr-layout-section">
        <livewire:dashboard.layouts.sidebar />
        <livewire:dashboard.layouts.navbar :$breadCrumb />


        <div class="cr-container-section">
            @if(Gate::allows('user_driver_index_rollcall_edit',App\Models\User::class))
            <div class="cr-card">
                <div class="cr-card-header">
                    <div class="cr-title">
                        <div>
                            <strong>حضور و غیاب {{$driver->name.' '.$driver->lastname}}</strong>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 col-lg-6">
                        <form wire:submit.prevent="save">
                            <div class="cr-card-body p-0">
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-md-12 col-12">
                                            <div class="row">
                                                <div class="col-md-2 col-12">
                                                    <div class="cr-text">
                                                        <label for="min">دقیقه</label>
                                                        <input type="text" class="text-center" wire:model="min">
                                                    </div>
                                                </div>
                                                <div class="col-md-2 col-12">
                                                    <div class="cr-text">
                                                        <label for="hour">ساعت</label>
                                                        <input type="text" class="text-center" wire:model="hour">
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>
                                                <div class="col-md-6 col-12">
                                                    <div class="cr-textarea">
                                                        <label for="description">توضیحات</label>
                                                        <textarea id="description" wire:model="description"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="cr-card-footer">
                                <div class="col-lg-4 col-md-6 col-12">
                                    <div class="cr-button">
                                        <label for=""></label>
                                        {{button('ثبت حضور','bx bxs-hand')}}
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="col-6 col-lg-6 ">
                        <form wire:submit.prevent="saveLocation">
                            <div class="cr-card-body p-0">
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-md-12 col-12 col-lg-12 " >
                                            <div  style="height: 100% !important;  width: 100% !important; min-height: 200px !important; display: block !important; position: relative !important;"  id="cr-map-section" wire:ignore></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="cr-card-footer">
                                <div class="col-lg-4 col-md-6 col-12">
                                    <div class="cr-button">
                                        <label for=""></label>
                                        {{button('ثبت موقعیت','bx bx-location-plus')}}
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            @endif
            <div id="paginated-list">
                <div class="cr-card">
                    <div class="cr-card-header">
                        <div class="cr-title">
                            <div>
                                <strong>لیست حضور غیاب</strong>
                                <div class="cr-stats">
                                    (حضور ماه جاری
                                    @php($rollCall = $driver->rollCallCurrentMonth())
                                    <strong>{{$rollCall->hour ? $rollCall->hour.' ساعت و ' : ''}}{{$rollCall->min ? $rollCall->min.' دقیقه' : ''}}</strong>
                                    )
                                    <button id="excel" class="btn btn-success">خروجی اکسل</button>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="cr-filter-section">
                            <div class="row">
                                <div class="col-lg-12 col-12">
                                    <div class="cr-filters">
                                        <div class="cr-date">
                                            <label>از تاریخ</label>
                                            <i class='bx bxs-calendar'></i>
                                            <input type="text" wire:model.live.debounce.500ms="dateFrom" id="dateFrom" value="{{$dateFrom}}" autocomplete="off">
                                        </div>
                                        <div class="cr-date">
                                            <label>تا تاریخ</label>
                                            <i class='bx bxs-calendar'></i>
                                            <input type="text" wire:model.live.debounce.500ms="dateTo" id="dateTo" value="{{$dateTo}}" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <livewire:dashboard.drivers.driver-rollcall-list :$driver/>
                </div>
            </div>
            <div id="paginated-list">
                <div class="cr-card">
                    <div class="cr-card-header">
                        <div class="cr-title">
                            <div>
                                <strong>لیست حضور غیاب ناموفق</strong>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <livewire:dashboard.drivers.driver-failed-rollcall-list :$driver/>
                </div>
            </div>


            <livewire:dashboard.layouts.footer/>
        </div>
    </div>
    {{toast($errors)}}
</div>
@script
<script>
    $(document).ready(function (){
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
        mark = L.marker(['36.27059642414889','59.55066296218155'],15).addTo(map);
        map.setView(['36.27059642414889','59.55066296218155'], 14);
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
        $('#dateFrom').persianDatepicker({
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
            onSelect: function(unix){
            @this.set('dateFrom', new persianDate(unix).format('YYYY/MM/DD'),true)
            }
        });
        $('#dateTo').persianDatepicker({
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
            onSelect: function(unix){
            @this.set('dateTo', new persianDate(unix).format('YYYY/MM/DD'),true)
            }
        });

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
            let url = '{{route('d.export.driver.rollCall')}}?user_id={{$driver->id}}&dateFrom='+query.dateFrom+'&dateTo='+query.dateTo;
            //console.log(url);
            window.location.href = url;
        });
    })
</script>
@endscript
