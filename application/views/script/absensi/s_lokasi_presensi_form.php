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
    let radius = parseInt(document.getElementById('radius_lokasi').value) || 500; // Default to 500 if empty

    // Create the circle with initial radius
    const circle = L.circle(marker.getLatLng(), {
        color: 'blue',
        fillColor: '#30f',
        fillOpacity: 0.2,
        radius: radius
    }).addTo(map);

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
        // Update the radius based on input value
        const newRadius = parseInt(event.target.value);
        if (!isNaN(newRadius) && newRadius > 0) {
            radius = newRadius / 1000; // Update radius variable
            circle.setRadius(radius); // Update the circle's radius
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