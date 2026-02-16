<div>
    <div class="cr-overlay"></div>
    <div class="cr-layout-section">
        <livewire:dashboard.layouts.sidebar />
        <livewire:dashboard.layouts.navbar :$breadCrumb />
        <div class="cr-container-section">
            <livewire:dashboard.settings.map-index-nav/>
            <div class="cr-card">
                <div class="cr-card-header">
                    <div class="cr-title">
                        <div>
                            <strong>نقشه و مناطق</strong>
                        </div>
                    </div>
                </div>  <div class="row">
                    <div class="col-3">

                        <div class="cr-filter-section">
                            <div class="cr-select">
                                <select id="city">
                                    <option value="">شهر را انتخاب کنید</option>
                                    @foreach ($options as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            <div class="cr-map-section" id="cr-map-section" wire:ignore></div>

            <livewire:dashboard.layouts.footer/>
        </div>
    </div>
    <script>
        let map = new L.Map("cr-map-section", {
            key: 'web.3dd03927cf104d018a4ce3cd6ec3c962',
            maptype: 'dreamy',
            center: [36.2966309, 59.6029849],
            zoom: 11,
            poi: true,
            traffic: false,
            zoomControl: false,
        });
        // گروهی برای ذخیره چندضلعی‌ها و اشکال رسم‌شده
        var drawnItems = new L.FeatureGroup();
        map.addLayer(drawnItems);

        // افزودن چندضلعی‌های قبلی به نقشه و گروه ویرایش

        let polygons = {!! json_encode($this->polygons) !!};
        let lPolygon
        $.each(polygons, function(i, item) {
            var lPolygon = L.polygon(JSON.parse(item.polygon), {
                color: item.color
            }).addTo(map);
            lPolygon.bindTooltip(item.region, {
                permanent: true,
                direction: "center"
            });
            drawnItems.addLayer(lPolygon); // اضافه کردن چندضلعی به گروه ویرایش
        });

        L.drawLocal.draw.toolbar.actions.title = 'بازگشت';
        L.drawLocal.draw.toolbar.actions.text = 'بازگشت';
        L.drawLocal.draw.toolbar.finish.title = 'اتمام';
        L.drawLocal.draw.toolbar.finish.text = 'اتمام';
        L.drawLocal.edit.toolbar.actions.save.title = 'ذخیره';
        L.drawLocal.edit.toolbar.actions.save.text = 'ذخیره';
        L.drawLocal.edit.toolbar.actions.cancel.title = 'لغو';
        L.drawLocal.edit.toolbar.actions.cancel.text = 'لغو';
        L.drawLocal.edit.toolbar.actions.clearAll.title= 'حذف همه';
        L.drawLocal.edit.toolbar.actions.clearAll.text = 'حذف همه';


        // ایجاد کنترل‌های ویرایش روی نقشه
        var drawControl = new L.Control.Draw({
            edit: {
                remove: {{auth()->user()->isDeveloper() ? 'true' : 'false'}},  // غیرفعال کردن گزینه حذف
                featureGroup: drawnItems, // اینجا باید از گروه FeatureGroup استفاده کنید
                poly: {
                    allowIntersection: false
                }
            },
            draw: {
                polygon: {
                    allowIntersection: false,  // جلوگیری از تقاطع چندضلعی‌ها
                    showArea: true,  // نمایش مساحت
                },
                rectangle: true,
                polyline: false,
                circle: false,
                marker: false,
                circlemarker : false
            }
        });
        map.addControl(drawControl);

        // رویداد برای رسم چندضلعی جدید
        map.on(L.Draw.Event.CREATED, function(e) {
            var layer = e.layer;
            var center = getPolygonCenter(layer);  // محاسبه مرکز

            // دریافت نام ناحیه
            var regionName = prompt("لطفاً نام ناحیه را وارد کنید:");

            // دریافت رنگ برای چندضلعی
            var polygonColor = prompt("لطفاً رنگ چندضلعی را به فرمت هگزادسیمال وارد کنید (مثلاً: #FF0000):", "#3388ff");

            // افزودن tooltip به چندضلعی
            layer.bindTooltip(regionName, {
                permanent: true,
                direction: "center"
            });

            // تنظیم رنگ چندضلعی
            layer.setStyle({
                color: polygonColor
            });

            // اضافه کردن چندضلعی به گروه
            drawnItems.addLayer(layer);

            // ذخیره کردن یا استفاده از چندضلعی به عنوان GeoJSON
            console.log(JSON.stringify(layer.toGeoJSON()));

            var geoJsonData = layer.toGeoJSON();  // تبدیل چندضلعی به فرمت GeoJSON
            geoJsonData.properties = {
                region: regionName,
                middle : center,
                color: polygonColor
            };
            Livewire.dispatch('storePolygon', {data : geoJsonData})

        });

        // رویداد برای ویرایش چندضلعی
        map.on(L.Draw.Event.EDITED, function(e) {
            var layers = e.layers;
            layers.eachLayer(function(layer) {
                var center = getPolygonCenter(layer);  // محاسبه مرکز
                // دریافت نام جدید ناحیه از کاربر (اختیاری)
                var lastRegionName = layer.getTooltip().getContent();
                var newRegionName = prompt("لطفاً نام جدید ناحیه را وارد کنید:", lastRegionName);

                // دریافت رنگ فعلی چندضلعی
                //var currentColor = layer.options.fillColor || layer.options.color || "#3388ff";

                // دریافت رنگ جدید برای چندضلعی از کاربر
                //var newPolygonColor = prompt("لطفاً رنگ جدید چندضلعی را به فرمت هگزادسیمال وارد کنید (مثلاً: #FF0000):", currentColor);

                // حذف tooltip قدیمی و اضافه کردن tooltip جدید
                layer.unbindTooltip();
                layer.bindTooltip(newRegionName, {
                    permanent: true,
                    direction: "center"
                });

                // اعمال رنگ جدید با استفاده از setStyle
                /*var newStyle = layer.setStyle({
                    fillColor: 'black',  // رنگ داخلی (fill)
                    color: 'black',      // رنگ مرزی (stroke)
                    fillOpacity: 0.5,            // می‌توانیم مقدار شفافیت را هم تنظیم کنیم
                    opacity: 1                   // شفافیت مرزی
                });
                console.log(newStyle);*/

                var geoJsonData = layer.toGeoJSON();  // تبدیل چندضلعی به فرمت GeoJSON
                geoJsonData.properties = {
                    lastRegion: lastRegionName,
                    newRegion: newRegionName,
                    middle : center
                    //color: newPolygonColor
                };
                Livewire.dispatch('updatePolygon', {data : geoJsonData})


            });
        });

        // رویداد برای رسم چندضلعی جدید
        map.on(L.Draw.Event.DELETED, function(e) {
            var layers = e.layers;
            layers.eachLayer(function(layer) {
                var region = layer.getTooltip().getContent();
                Livewire.dispatch('deletePolygon', {region : region})
            });
        });


        function getPolygonCenter(polygon) {
            var latlngs = polygon.getLatLngs()[0];  // دریافت مختصات نقاط چندضلعی
            var latSum = 0, lngSum = 0, count = latlngs.length;

            latlngs.forEach(function(latlng) {
                latSum += latlng.lat;
                lngSum += latlng.lng;
            });

            return L.latLng(latSum / count, lngSum / count);  // محاسبه میانگین مختصات
        }

        $(document).on('change','#city',function (){
            Livewire.dispatch('city', {city: $(this).val()})

        })
    </script>

</div>
