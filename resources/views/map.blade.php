<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta SPKLU</title>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <style>
        #map-container {
            position: relative;
            flex: 1;
        }
        #google-map, #leaflet-map {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        #leaflet-map {
            display: none;
        }
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        input:checked + .slider {
            background-color: #007bff;
        }
        input:checked + .slider:before {
            transform: translateX(26px);
        }
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color: rgb(0,0,0); 
            background-color: rgba(0,0,0,0.4); 
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto; 
            padding: 20px;
            border: 1px solid #888;
            width: 80%; 
            max-width: 300px; /* Adjust the max-width to make the modal smaller */
        }
        .open-google-maps-btn {
            margin-top: 10px;
            padding: 5px 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .open-google-maps-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body class="flex flex-col h-screen">
    <header class="flex justify-between items-center p-4 bg-gray-100 border-b border-gray-300">
        <h1 class="text-2xl">SPKLU Map Bali</h1>
        <div class="flex items-center">
            <label class="switch mr-4">
                <input type="checkbox" id="map-switch">
                <span class="slider"></span>
            </label>
            <button onclick="location.href='/admin'" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-700">Login</button>
        </div>
    </header>
    <div id="map-container" class="relative flex-1">
        <div id="google-map" class="absolute top-0 left-0 w-full h-full"></div>
        <div id="leaflet-map" class="absolute top-0 left-0 w-full h-full"></div>
    </div>
    <footer class="p-4 bg-gray-100 border-t border-gray-300 text-center">
        &copy; 2025 SPKLU Map Bali. All rights reserved.
    </footer>
    <script>
        async function initMaps() {
            // Initialize Google Map
            const googleMap = new google.maps.Map(document.getElementById('google-map'), {
                center: { lat: -8.65, lng: 115.2167 },
                zoom: 10,
            });

            // Fetch locations
            const response = await fetch('/api/info-lokasi');
            const locations = await response.json();

            let currentInfoWindow = null;

            // Add markers to Google Map
            locations.forEach(location => {
                const marker = new google.maps.Marker({
                    position: { lat: parseFloat(location.latitude), lng: parseFloat(location.longitude) },
                    map: googleMap,
                    title: location.nama_tempat
                });

                const infowindow = new google.maps.InfoWindow({
                    content: `<div class="info-window-content">
                                    <h3 class="text-lg font-bold">${location.nama_tempat}</h3>
                                    <p class="text-sm text-gray-600">${location.deskripsi_tempat}</p>
                                <button class="open-google-maps-btn" onclick="window.open('https://www.google.com/maps?q=${location.latitude},${location.longitude}', '_blank')">View in Google Maps</button>
                              </div>`
                });

                marker.addListener('click', function() {
                    if (currentInfoWindow) {
                        currentInfoWindow.close();
                    }
                    infowindow.open(googleMap, marker);
                    currentInfoWindow = infowindow;
                });
            });

            // Initialize Leaflet Map
            const leafletMap = L.map('leaflet-map').setView([-8.65, 115.2167], 10);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(leafletMap);

            // Add markers to Leaflet Map
            locations.forEach(location => {
                const marker = L.marker([location.latitude, location.longitude]).addTo(leafletMap);
                marker.bindPopup(`<div class="info-window-content">
                                        <h3 class="text-lg font-bold">${location.nama_tempat}</h3>
                                        <p class="text-sm text-gray-600">${location.deskripsi_tempat}</p>
                                    <button class="open-google-maps-btn" onclick="window.open('https://www.google.com/maps?q=${location.latitude},${location.longitude}', '_blank')">View in Google Maps</button>
                                  </div>`);
            });

            // Switch map functionality
            document.getElementById('map-switch').addEventListener('change', (event) => {
                const googleMapDiv = document.getElementById('google-map');
                const leafletMapDiv = document.getElementById('leaflet-map');

                if (event.target.checked) {
                    googleMapDiv.style.display = 'none';
                    leafletMapDiv.style.display = 'block';
                    leafletMap.invalidateSize(); // Ensure Leaflet map is properly rendered
                } else {
                    googleMapDiv.style.display = 'block';
                    leafletMapDiv.style.display = 'none';
                }
            });
        }

        window.onload = initMaps;
    </script>
</body>
</html>