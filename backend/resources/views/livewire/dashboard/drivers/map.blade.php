<div>

    <style>
        #map {
            height: 100vh;
        }
        .number-icon {
            background-color: #2A81CB;
            color: #fff;
            font-weight: bold;
            font-size: 14px;
            border-radius: 50%;
            text-align: center;
            line-height: 30px;
            border: 2px solid #fff;
            box-shadow: 0 0 3px #000;
        }

    </style>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />

    <div id="map"></div>
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>

    @script

    <script>
        $(document).ready(function (){
            const map = L.map('map').setView([36.3049, 59.5128], 14);
            console.log('sssss')
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
            }).addTo(map);

            const locations = @json($locations);

            locations.forEach((location, index) => {
                let color = "blue"; // default

                if (location.status === 10) {
                    color = "yellow";
                } else if (location.status === 1) {
                    color = "grey";
                }
                else if (location.status === 2) {
                    color = "green";
                }
                else if (location.status === 3) {
                    color = "purple";
                }
                const numberIcon = L.divIcon({
                    html: `<div style="background:${color}; width:32px; height:32px; border-radius:50%;
                display:flex; align-items:center; justify-content:center;
                color:black; font-weight:bold;">
                ${index + 1}
              </div>`,
                    className: "",
                    iconSize: [32, 32]
                });

                L.marker([location.lat, location.lon], { icon: numberIcon })
                    .bindTooltip(` ${location.start} ساعت:  ${location.submit_id} شناسه درخواست : `, {
                        permanent: false,
                        direction: 'top',
                        offset: [0, -10]
                    })
                    .addTo(map);
            });
        })

    </script>
    @endscript
</div>
