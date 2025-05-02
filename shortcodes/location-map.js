// Attach initMap to the global window object
window.initMap = function () {
    const defaultLocation = { lat: 44.3615, lng: 26.1279 }; // Default location: Berceni, București

    const map = new google.maps.Map(document.getElementById('map'), {
        center: defaultLocation,
        zoom: 14,
    });

    const marker = new google.maps.Marker({
        position: defaultLocation,
        map: map,
        draggable: true, // Allow the pin to be draggable
    });

    // Update the hidden input fields when the marker is dragged
    marker.addListener('dragend', function () {
        document.getElementById('location_lat').value = marker.getPosition().lat();
        document.getElementById('location_lng').value = marker.getPosition().lng();
    });

    // Update the marker position and hidden inputs when the map is clicked
    map.addListener('click', function (event) {
        marker.setPosition(event.latLng);
        document.getElementById('location_lat').value = event.latLng.lat();
        document.getElementById('location_lng').value = event.latLng.lng();
    });

    // Set initial hidden input values
    document.getElementById('location_lat').value = defaultLocation.lat;
    document.getElementById('location_lng').value = defaultLocation.lng;
};
