<?php
$locations = $attributes['locations'] ?? [];
?>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<div id="leaflet-map" style="height: 400px;"></div>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var map = L.map('leaflet-map').setView([45.9432, 24.9668], 6);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        const locations = <?php echo json_encode($locations); ?>;

        locations.forEach(loc => {
            const [lat, lng] = loc.split(',');
            if (!isNaN(lat) && !isNaN(lng)) {
                L.marker([parseFloat(lat), parseFloat(lng)]).addTo(map);
            }
        });
    });
</script>
