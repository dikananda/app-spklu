<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin SPKLU</title>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <style>
        .overlay {
            z-index: 9998;
        }
        .modal {
            z-index: 9999;
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
            background-color: #2196F3;
        }
        input:checked + .slider:before {
            transform: translateX(26px);
        }
    </style>
</head>
<body class="flex flex-col h-screen font-sans">
    <header class="flex justify-between items-center p-4 bg-gray-100 border-b border-gray-300">
        <h1 class="text-2xl">SPKLU Map Bali - Admin</h1>
        <div class="flex items-center">
            <label class="switch mr-4">
                <input type="checkbox" id="map-switch">
                <span class="slider"></span>
            </label>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-700 ml-4">Logout</button>
            </form>
        </div>
    </header>
    <div id="map-container" class="relative flex-1">
        <div id="google-map" class="absolute top-0 left-0 w-full h-full"></div>
        <div id="leaflet-map" class="absolute top-0 left-0 w-full h-full" style="display: none;"></div>
    </div>
    <footer class="p-4 bg-gray-100 border-t border-gray-300 text-center">
        &copy; 2025 SPKLU Map Bali. All rights reserved.
    </footer>
    <div class="overlay fixed inset-0 bg-black bg-opacity-50 hidden" id="overlay"></div>
    <div class="modal fixed inset-0 flex items-center justify-center hidden" id="modal">
        <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-lg">
            <div class="modal-header flex justify-between items-center border-b pb-2 mb-4">
                <h3 id="modalTitle" class="text-xl">Tambah Lokasi SPKLU</h3>
                <button class="close-btn text-2xl" id="closeModal">&times;</button>
            </div>
            <div class="modal-body mb-4">
                <form id="locationForm">
                    <div class="mb-4">
                        <label for="nama_tempat" class="block text-sm font-medium text-gray-700">Nama Tempat:</label>
                        <input type="text" id="nama_tempat" name="nama_tempat" required class="mt-1 p-2 border border-gray-300 rounded w-full">
                    </div>
                    <div class="mb-4">
                        <label for="deskripsi_tempat" class="block text-sm font-medium text-gray-700">Deskripsi Tempat:</label>
                        <textarea id="deskripsi_tempat" name="deskripsi_tempat" required class="mt-1 p-2 border border-gray-300 rounded w-full"></textarea>
                    </div>
                    <input type="hidden" id="latitude" name="latitude">
                    <input type="hidden" id="longitude" name="longitude">
                    <input type="hidden" id="location_id" name="location_id">
                </form>
            </div>
            <div class="modal-footer flex justify-end">
                <button type="submit" form="locationForm" id="saveButton" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-700">Simpan</button>
                <button type="button" id="closeModalFooter" class="ml-2 px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-700">Batal</button>
            </div>
        </div>
    </div>
    <div class="overlay fixed inset-0 bg-black bg-opacity-50 hidden" id="successOverlay"></div>
    <div class="modal fixed inset-0 flex items-center justify-center hidden" id="successModal">
        <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-lg">
            <div class="modal-header flex justify-between items-center border-b pb-2 mb-4">
                <h3 class="text-xl text-gray-700">Success</h3>
                <button class="close-btn text-2xl" id="closeSuccessModal">&times;</button>
            </div>
            <div class="modal-body mb-4 text-green-700">
                <p>Data berhasil disimpan!</p>
            </div>
            <div class="modal-footer flex justify-end">
                <button type="button" id="closeSuccessModalFooter" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-700">Tutup</button>
            </div>
        </div>
    </div>

    <div class="overlay fixed inset-0 bg-black bg-opacity-50 hidden" id="deleteConfirmOverlay"></div>
    <div class="modal fixed inset-0 flex items-center justify-center hidden" id="deleteConfirmModal">
        <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-lg">
            <div class="modal-header flex justify-between items-center border-b pb-2 mb-4">
                <h3 class="text-xl text-grey-700">Konfirmasi</h3>
                <button class="close-btn text-2xl" id="closeDeleteConfirmModal">&times;</button>
            </div>
            <div class="modal-body mb-4 text-gray-700">
                <p>Apakah Anda yakin ingin menghapus data ini?</p>
            </div>
            <div class="modal-footer flex justify-end">
                <button type="button" id="confirmDeleteButton" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-700">Hapus</button>
                <button type="button" id="closeDeleteConfirmFooter" class="ml-2 px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-700">Batal</button>
            </div>
        </div>
    </div>
    <div class="overlay fixed inset-0 bg-black bg-opacity-50 hidden" id="deleteSuccessOverlay"></div>
    <div class="modal fixed inset-0 flex items-center justify-center hidden" id="deleteSuccessModal">
        <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-lg">
            <div class="modal-header flex justify-between items-center border-b pb-2 mb-4">
                <h3 class="text-xl text-gray-700">Sukses</h3>
                <button class="close-btn text-2xl" id="closeDeleteSuccessModal">&times;</button>
            </div>
            <div class="modal-body mb-4 text-red-700">
                <p>Data berhasil dihapus!</p>
            </div>
            <div class="modal-footer flex justify-end">
                <button type="button" id="closeDeleteSuccessFooter" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-700">Tutup</button>
            </div>
        </div>
    </div>
    <script>
        let map, selectedLocation;

        function initMaps() {
            // Initialize Google Map
            const googleMap = new google.maps.Map(document.getElementById('google-map'), {
                center: { lat: -8.65, lng: 115.2167 },
                zoom: 10,
            });

            // Event listener untuk klik pada peta
            googleMap.addListener("click", (e) => {
                const lat = e.latLng.lat();
                const lng = e.latLng.lng();

                // Tampilkan modal untuk menambah lokasi baru
                showModal(lat, lng);
            });

            // Fetch locations
            fetch('/api/info-lokasi')
                .then(response => response.json())
                .then(locations => {
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
                                        <button class="edit-btn px-2 py-1 bg-green-500 text-white rounded hover:bg-green-700 mt-4" onclick="editLocation(${location.id}, '${location.nama_tempat}', '${location.deskripsi_tempat}', ${location.latitude}, ${location.longitude})">Edit</button>
                                        <button class="delete-btn px-2 py-1 bg-red-500 text-white rounded hover:bg-red-700 mt-4" onclick="showDeleteConfirmModal(${location.id})">Delete</button>

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
                                            <button class="edit-btn px-2 py-1 bg-green-500 text-white rounded hover:bg-green-700" onclick="editLocation(${location.id}, '${location.nama_tempat}', '${location.deskripsi_tempat}', ${location.latitude}, ${location.longitude})">Edit</button>
                                            <button class="delete-btn px-2 py-1 bg-red-500 text-white rounded hover:bg-red-700" onclick="showDeleteConfirmModal(${location.id})">Delete</button>
                                          </div>`);
                    });

                    // Event listener untuk klik pada peta di Leaflet
                    leafletMap.on('click', function(e) {
                        const lat = e.latlng.lat;
                        const lng = e.latlng.lng;

                        // Tampilkan modal untuk menambah lokasi baru
                        showModal(lat, lng);
                    });

                    // Switch map functionality
                    const mapSwitch = document.getElementById('map-switch');
                    mapSwitch.addEventListener('change', (event) => {
                        const googleMapDiv = document.getElementById('google-map');
                        const leafletMapDiv = document.getElementById('leaflet-map');

                        if (event.target.checked) {
                            googleMapDiv.style.display = 'none';
                            leafletMapDiv.style.display = 'block';
                            leafletMap.invalidateSize(); // Ensure Leaflet map is properly rendered
                            localStorage.setItem('activeMap', 'leaflet');
                        } else {
                            googleMapDiv.style.display = 'block';
                            leafletMapDiv.style.display = 'none';
                            localStorage.setItem('activeMap', 'google');
                        }
                    });

                    // Set initial map based on localStorage
                    const activeMap = localStorage.getItem('activeMap') || 'google';
                    if (activeMap === 'leaflet') {
                        mapSwitch.checked = true;
                        document.getElementById('google-map').style.display = 'none';
                        document.getElementById('leaflet-map').style.display = 'block';
                        leafletMap.invalidateSize();
                    } else {
                        document.getElementById('google-map').style.display = 'block';
                        document.getElementById('leaflet-map').style.display = 'none';
                    }
                });
        }

        function showModal(lat, lng, id = null, nama_tempat = '', deskripsi_tempat = '') {
            document.getElementById("latitude").value = lat;
            document.getElementById("longitude").value = lng;
            document.getElementById("location_id").value = id;
            document.getElementById("nama_tempat").value = nama_tempat;
            document.getElementById("deskripsi_tempat").value = deskripsi_tempat;
            document.getElementById("modalTitle").innerText = id ? 'Edit Lokasi SPKLU' : 'Tambah Lokasi SPKLU';
            document.getElementById("modal").classList.add("flex");
            document.getElementById("modal").classList.remove("hidden");
            document.getElementById("overlay").classList.add("block");
            document.getElementById("overlay").classList.remove("hidden");
        }

        function hideModal() {
            document.getElementById("modal").classList.add("hidden");
            document.getElementById("modal").classList.remove("flex");
            document.getElementById("overlay").classList.add("hidden");
            document.getElementById("overlay").classList.remove("block");
        }

        function showSuccessModal() {
            document.getElementById("successModal").classList.add("flex");
            document.getElementById("successModal").classList.remove("hidden");
            document.getElementById("successOverlay").classList.add("block");
            document.getElementById("successOverlay").classList.remove("hidden");
            setTimeout(() => {
                hideSuccessModal();
                location.reload(); // Refresh halaman untuk memuat marker baru
            }, 1000); // 1 detik
        }

        function hideSuccessModal() {
            document.getElementById("successModal").classList.add("hidden");
            document.getElementById("successModal").classList.remove("flex");
            document.getElementById("successOverlay").classList.add("hidden");
            document.getElementById("successOverlay").classList.remove("block");
        }

        function editLocation(id, nama_tempat, deskripsi_tempat, lat, lng) {
            showModal(lat, lng, id, nama_tempat, deskripsi_tempat);
        }

        let deleteLocationId = null;

        function showDeleteConfirmModal(id) {
            deleteLocationId = id;
            document.getElementById("deleteConfirmModal").classList.add("flex");
            document.getElementById("deleteConfirmModal").classList.remove("hidden");
            document.getElementById("deleteConfirmOverlay").classList.add("block");
            document.getElementById("deleteConfirmOverlay").classList.remove("hidden");
        }

        function hideDeleteConfirmModal() {
            deleteLocationId = null;
            document.getElementById("deleteConfirmModal").classList.add("hidden");
            document.getElementById("deleteConfirmModal").classList.remove("flex");
            document.getElementById("deleteConfirmOverlay").classList.add("hidden");
            document.getElementById("deleteConfirmOverlay").classList.remove("block");
        }

        function showDeleteSuccessModal() {
            document.getElementById("deleteSuccessModal").classList.add("flex");
            document.getElementById("deleteSuccessModal").classList.remove("hidden");
            document.getElementById("deleteSuccessOverlay").classList.add("block");
            document.getElementById("deleteSuccessOverlay").classList.remove("hidden");
            setTimeout(() => {
                hideDeleteSuccessModal();
                location.reload(); // Refresh halaman untuk memuat marker baru
            }, 1000); // 1 detik
        }

        function hideDeleteSuccessModal() {
            document.getElementById("deleteSuccessModal").classList.add("hidden");
            document.getElementById("deleteSuccessModal").classList.remove("flex");
            document.getElementById("deleteSuccessOverlay").classList.add("hidden");
            document.getElementById("deleteSuccessOverlay").classList.remove("block");
        }

        document.getElementById("confirmDeleteButton").addEventListener("click", () => {
            if (deleteLocationId) {
                fetch(`/api/info-lokasi/${deleteLocationId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                })
                .then(response => {
                    if (response.ok) {
                        showDeleteSuccessModal();
                        hideDeleteConfirmModal();
                    } else {
                        return response.json().then(error => {
                            throw new Error(error.message);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert("Gagal menghapus data: " + error.message);
                });
            }
        });

        document.getElementById("closeDeleteConfirmModal").addEventListener("click", hideDeleteConfirmModal);
        document.getElementById("closeDeleteConfirmFooter").addEventListener("click", hideDeleteConfirmModal);
        document.getElementById("closeDeleteSuccessModal").addEventListener("click", hideDeleteSuccessModal);
        document.getElementById("closeDeleteSuccessFooter").addEventListener("click", hideDeleteSuccessModal);
        // Submit form
        document.getElementById("locationForm").addEventListener("submit", (e) => {
            e.preventDefault();

            const formData = {
                id: document.getElementById("location_id").value,
                nama_tempat: document.getElementById("nama_tempat").value,
                deskripsi_tempat: document.getElementById("deskripsi_tempat").value,
                latitude: document.getElementById("latitude").value,
                longitude: document.getElementById("longitude").value,
            };

            const method = formData.id ? 'PUT' : 'POST';
            const url = formData.id ? `/api/info-lokasi/${formData.id}` : '/api/info-lokasi';

            // Kirim data menggunakan AJAX
            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData),
            })
                .then(response => {
                    if (response.ok) {
                        showSuccessModal();
                        hideModal();
                    } else {
                        return response.json().then(error => {
                            throw new Error(error.message);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showSuccessModal("Gagal menyimpan data: " + error.message);
                });
        });

        document.getElementById("closeModal").addEventListener("click", hideModal);
        document.getElementById("closeModalFooter").addEventListener("click", hideModal);
        document.getElementById("closeSuccessModal").addEventListener("click", hideSuccessModal);
        document.getElementById("closeSuccessModalFooter").addEventListener("click", hideSuccessModal);

        window.onload = initMaps;
    </script>
</body>
</html>