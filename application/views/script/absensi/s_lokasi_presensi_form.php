<!-- Leaflet  -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin="anonymous"></script>

<script>
    // Initialize the map
    <?php
    if ($this->uri->segment(3) == Null) {
    ?>
        const map = L.map('map').setView([-6.2568425826630625, 106.88298401638922], 13); // Centered on Jakarta, Indonesia
        const marker = L.marker([-6.2568425826630625, 106.88298401638922], {
            draggable: true
        }).addTo(map);
    <?php
    } else {
    ?>
        const map = L.map('map').setView([<?= $detail->latitude ?>, <?= $detail->longitude ?>], 13); // Centered on a specific location
        const marker = L.marker([<?= $detail->latitude ?>, <?= $detail->longitude ?>], {
            draggable: true
        }).addTo(map);
    <?php
    }
    ?>

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Default radius (can be changed based on user input)
    let radius = parseInt(document.getElementById('radius_lokasi').value) || 100; // Default to 500 if empty

    // Create the circle with initial radius
    const circle = L.circle(marker.getLatLng(), {
        color: 'blue',
        fillColor: '#30f',
        fillOpacity: 0.2,
        radius: radius
    }).addTo(map);

    // Define a custom red icon for the user's location
    const RedIcon = new L.Icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    });

    // Initialize the user location marker (will be added to the map only on success)
    let userMarker = null;

    // --- END NEW CODE ---


    // -------------------------------------------------------------------------
    // MODIFIED GEOLOCATION LOGIC
    // -------------------------------------------------------------------------

    function onLocationFound(e) {
        const latlng = e.latlng;
        const accuracy = e.accuracy; // in meters

        // If the user marker hasn't been created yet, create it and add it to the map
        if (userMarker === null) {
            userMarker = L.marker(latlng, {
                icon: RedIcon,
                draggable: false // User location marker should not be draggable
            }).addTo(map);
            userMarker.bindPopup(`You are here (± ${Math.round(accuracy)}m)`).openPopup();
        } else {
            // If it exists, just update its position
            userMarker.setLatLng(latlng);
            userMarker.setPopupContent(`You are here (± ${Math.round(accuracy)}m)`);
        }

        // You can optionally move the map to the user's location, but 
        // we'll keep it focused on the main marker's location by default.
        // map.setView(latlng, 16); 

        // Optional: draw a circle around the user's location showing accuracy (different from input radius)
        L.circle(latlng, {
            radius: accuracy,
            color: 'red',
            fillColor: '#f03',
            fillOpacity: 0.1
        }).addTo(map);
    }

    function onLocationError(e) {
        console.warn("Geolocation failed:", e.message);
        // You can add logic here to remove the marker if it was previously set, though 
        // typically an error means it was never found in the first place.
    }

    // 1. Attach the event listeners to the map
    map.on('locationfound', onLocationFound);
    map.on('locationerror', onLocationError);

    // 2. Request the user's location
    map.locate({
        watch: false, // Set to true to continuously track, false for a one-time fix
        setView: false, // Don't automatically move the map to the user's location
        maxZoom: 16,
        enableHighAccuracy: true
    });

    // -------------------------------------------------------------------------
    // END MODIFIED GEOLOCATION LOGIC
    // -------------------------------------------------------------------------


    // Event listener for marker drag
    marker.on('dragend', () => {
        const latLng = marker.getLatLng();
        updateLocation(latLng.lat, latLng.lng);

        // Update circle position
        circle.setLatLng(latLng);
    });

    // Function to update location fields
    function updateLocation(lat, lng) {
        document.getElementById('latitude_lokasi').value = lat;
        document.getElementById('longitude_lokasi').value = lng;

        // Fetch address using Nominatim API
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
            .then(response => response.json())
            .then(data => {
                const address = data.display_name || "Unknown Address";
                const name = data.address.road || "Unknown Location";

                document.getElementById('nama_lokasi').value = name;
                document.getElementById('alamat_lokasi').value = address;
            })
            .catch(error => console.error("Error fetching address:", error));
    }

    // Event listener for radius input change
    document.getElementById('radius_lokasi').addEventListener('input', (event) => {
        // Get the new radius value directly
        const newRadius = parseInt(event.target.value);

        // Make sure the new value is a valid number and greater than 0
        if (!isNaN(newRadius) && newRadius > 0) {
            // radius = newRadius / 1000; // Update radius variable

            // Now, pass the newRadius directly to setRadius() without dividing
            circle.setRadius(newRadius);
        }
    });

    // Event listener for longitude and latitude input change (for both longitude and latitude)
    document.getElementById('longitude_lokasi').addEventListener('input', (event) => {
        const lat = parseFloat(document.getElementById('latitude_lokasi').value);
        const lng = parseFloat(event.target.value);

        if (!isNaN(lat) && !isNaN(lng)) {
            marker.setLatLng([lat, lng]); // Update marker position
            circle.setLatLng([lat, lng]); // Update circle position

            // Update location fields with the new values
            updateLocation(lat, lng);
        }
    });

    document.getElementById('latitude_lokasi').addEventListener('input', (event) => {
        const lat = parseFloat(event.target.value);
        const lng = parseFloat(document.getElementById('longitude_lokasi').value);

        if (!isNaN(lat) && !isNaN(lng)) {
            marker.setLatLng([lat, lng]); // Update marker position
            circle.setLatLng([lat, lng]); // Update circle position

            // Update location fields with the new values
            updateLocation(lat, lng);
        }
    });
</script>