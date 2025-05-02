<?php
$locations = $attributes['locations'] ?? [];
$mapHeight = $attributes['mapHeight'] ?? 450;
$mapId = !empty($attributes['mapId']) ? $attributes['mapId'] : 'leaflet-map-' . uniqid();
?>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<div id="<?php echo esc_attr($mapId); ?>" style="height: <?php echo intval($mapHeight); ?>px;"></div>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
	var map = L.map('<?php echo esc_js($mapId); ?>').setView([45.9432, 24.9668], 6);
	L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

	const locations = <?php echo json_encode($locations); ?>;

	locations.forEach(loc => {
		const marker = L.marker([loc.lat, loc.lng]).addTo(map);
		if (loc.info) marker.bindPopup(loc.info);
	});
});
</script>
