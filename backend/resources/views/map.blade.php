<!-- resources/views/map.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Map View</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <style>
        #map {
            height: 500px;
            width: 100%;
        }
    </style>
</head>
<body>
<div id="map"></div>
<script>
    // Initialize the map
    var map = L.map('map').setView([36.39061480, 59.50769400], 12);

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
    }).addTo(map);

    // Default markers (red by default)
    var defaultCoordinates = [
        { lat: 36.39061480, lng: 59.50769400 },
        { lat: 36.33975590, lng: 59.65504210 },
        { lat: 36.34574340, lng: 59.52481450 },
        { lat: 36.34606220, lng: 59.54785440 },
    ];

    // Green markers
    var greenCoordinates = [
        { lat: 36.3541907, lng: 59.4809118 ,d:60438 },
        { lat: 36.3615714, lng: 59.4890270 ,d:60438},
        { lat: 36.3563509, lng: 59.4853663 ,d:69198},
        { lat: 36.3609321, lng: 59.4891252 ,d:69198},
    ];

    // Add a custom green icon
    var greenIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
        shadowSize: [41, 41],
    });

    // Add default markers to the map
    defaultCoordinates.forEach(function (coord) {
        var marker = L.marker([coord.lat, coord.lng]).addTo(map);

        // Bind tooltip to display lat & lng on hover
        marker.bindTooltip(`Lat: ${coord.lat}, Lng: ${coord.lng}`, {
            permanent: false,
            direction: 'top',
        });
    });

    // Add green markers to the map
    greenCoordinates.forEach(function (coord) {
        var marker = L.marker([coord.lat, coord.lng], { icon: greenIcon }).addTo(map);

        // Bind tooltip to display lat & lng on hover
        marker.bindTooltip(`Lat: ${coord.lat}, Lng: ${coord.lng}`, {
            permanent: false,
            direction: 'top',
        });
    });
</script>
</body>
</html>
