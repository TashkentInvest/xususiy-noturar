@extends('layouts.admin')

@section('content')
    <div class="container mt-5">
        <div class="card shadow-lg p-4 rounded-4 border-0">
            <h2 class="mb-4 text-center text-primary fw-bold">Ер Тўла Яратиш</h2>

            <form action="{{ route('yertola.store') }}" method="POST">
                @csrf

                <!-- Manzil tanlash -->
                <div class="mb-4">
                    <label class="form-label fw-bold">📍 Манзилни танланг:</label>
                    <div class="row">
                        <div class="col-md-6">
                            <select name="sub_street_id" class="form-select form-control-lg shadow-sm" required>
                                <option value="">📌 Қўшимча кўча</option>
                                @foreach ($subStreets as $subStreet)
                                    <option value="{{ $subStreet->id }}">{{ $subStreet->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <select name="street_id" class="form-select form-control-lg shadow-sm" required>
                                <option value="">🏡 Кўча</option>
                                @foreach ($streets as $street)
                                    <option value="{{ $street->id }}">{{ $street->name }}</option>
                                @endforeach
                            </select>
                        </div>


                    </div>
                    <div class="row">
                        <!-- Right Column -->
                        <div class="col-lg-6 col-md-12 col-12 mt-3">
                            <div class="mb-3">
                                <label class="text-danger">Файлларни юклаш (Камида 4 та расм мажбурий)</label>
                            </div>

                            <div id="fileInputsContainer">
                                @for ($i = 1; $i <= 4; $i++)
                                    <div class="mb-3" id="fileInput{{ $i }}">
                                        <label for="file{{ $i }}">Файл {{ $i }}</label>
                                        <div class="input-group">
                                            <input type="file" class="form-control" name="files[]"
                                                id="file{{ $i }}" accept="image/*" required>
                                            <button type="button" class="btn btn-secondary"
                                                onclick="openCameraModal('file{{ $i }}')">📷</button>
                                        </div>
                                    </div>
                                @endfor
                            </div>

                            <div id="file-error" class="text-danger mb-3"></div>
                            <div id="file-upload-container"></div>
                            <button type="button" class="btn btn-secondary mb-3" id="add-file-btn">Янги файл
                                қўшиш</button>


                        </div>
                        <div class="col-lg-6 col-md-12 col-12 mt-3">
                            <div class="mb-3">
                                <button id="find-my-location" type="button" class="btn btn-primary mb-3">Менинг
                                    жойлашувимни
                                    топиш</button>
                                <div id="map" style="height: 500px; width: 100%;"></div>
                                @error('latitude')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                                @error('longitude')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                            <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
                            <div class="mb-3">
                                <label for="geolokatsiya">Геолокация (координата)</label>
                                <input class="form-control" type="text" name="geolokatsiya" id="geolokatsiya" readonly
                                    required value="{{ old('geolokatsiya') }}">
                                @error('geolokatsiya')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hidden Field -->
                <input type="hidden" name="is_status_yer_tola" value="true">

                <!-- Ер тўла мавжудми? -->
                <div class="mb-4">
                    <label class="form-label fw-bold">🏠 Ер тўла мавжудми?</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input custom-radio" type="radio" name="does_exists_yer_tola"
                                value="1" onclick="showExtraFields(true)">
                            <label class="form-check-label fw-bold">✅ Мавжуд</label>
                        </div>
                        <div class="form-check ml-3">
                            <input class="form-check-input custom-radio" type="radio" name="does_exists_yer_tola"
                                value="0" onclick="showExtraFields(false)">
                            <label class="form-check-label fw-bold">❌ Мавжуд эмас</label>
                        </div>
                    </div>
                </div>

                <!-- Агар мавжуд бўлса -->
                <div id="extraFields" class="mb-4 p-3 border rounded bg-light shadow-sm" style="display: none;">
                    <label class="form-label fw-bold">🔧 Бошқарув шакли:</label>
                    <select name="managed_by" id="managedBy" class="form-select form-control-lg shadow-sm"
                        onchange="toggleBalanceFields()">
                        <option value="">Танланг</option>
                        <option value="Kompaniya">🏢 Компания</option>
                        <option value="O'z o'zini boshqaradi">👤 Ўз-ўзини бошқаради</option>
                    </select>

                    <div class="mt-3">
                        <input type="text" name="balance_keeper" class="form-control form-control-lg shadow-sm mb-2"
                            placeholder="🔹 Балансга масъул шахс">
                        <input type="text" name="stir" id="stirField"
                            class="form-control form-control-lg shadow-sm" placeholder="📊 СТИР рақами"
                            style="display: none;">
                    </div>

                    <!-- Фойдаланиш мумкинми? -->
                    <div class="mt-4">
                        <label class="form-label fw-bold">❓ Фойдаланиш мумкинми?</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input custom-radio" type="radio"
                                    name="does_can_we_use_yer_tola" value="1" onclick="showUseFields(true)">
                                <label class="form-check-label fw-bold">✅ Ҳа</label>
                            </div>
                            <div class="form-check ml-3">
                                <input class="form-check-input custom-radio" type="radio"
                                    name="does_can_we_use_yer_tola" value="0" onclick="showUseFields(false)">
                                <label class="form-check-label fw-bold">❌ Йўқ</label>
                            </div>
                        </div>
                    </div>

                    <!-- Агар фойдаланиш мумкин бўлса -->
                    <div id="useFields" class="mt-4 p-3 border rounded bg-light shadow-sm" style="display: none;">
                        <input type="number" name="ijaraga_berilgan_qismi_yer_tola"
                            class="form-control form-control-lg shadow-sm mb-2"
                            placeholder="📏 Ижарага берилган қисм (м²)">
                        <input type="number" name="ijaraga_berilмаган_qismi_yer_tola"
                            class="form-control form-control-lg shadow-sm mb-2"
                            placeholder="📏 Ижарага берилмаган қисм (м²)">
                        <input type="number" name="texnik_qismi_yer_tola" class="form-control form-control-lg shadow-sm"
                            placeholder="⚙ Техник қисм (м²)">

                        <!-- Ижара нархи -->
                        <div class="mb-3 mt-3">
                            <label class="form-label fw-bold">💰 Ойлик ижара нархи:</label>
                            <input type="number" name="oylik_ijara_narxi_yer_tola"
                                class="form-control form-control-lg shadow-sm" placeholder="💵 Сум">
                        </div>

                        <!-- Фаолият тури (Checkbox) -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">🏢 Фаолият тури:</label>
                            <div class="d-flex flex-wrap gap-2">
                                @php
                                    $faoliyatTurlari = [
                                        'Gozallik Saloni' => '💄 Гўзаллик салони',
                                        'Dorixona' => '💊 Дорихона',
                                        'Kompyuter Xizmati' => '💻 Компьютер хизмати',
                                        'Savdo' => '🛍 Савдо',
                                        'Boshqalar' => '🔹 Бошқалар',
                                    ];
                                @endphp
                                @foreach ($faoliyatTurlari as $key => $value)
                                    <div class="form-check d-flex align-items-center ml-3">
                                        <input class="form-check-input m-0" type="checkbox" name="faoliyat_turi[]"
                                            value="{{ $key }}">
                                        <label class="form-check-label">{{ $value }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Юбориш тугмаси -->
                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm fw-bold">💾 Сақлаш</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts for Dynamic Fields -->
    <script>
        function showExtraFields(show) {
            document.getElementById('extraFields').style.display = show ? 'block' : 'none';
        }

        function toggleBalanceFields() {
            var managedBy = document.getElementById('managedBy').value;
            document.getElementById('stirField').style.display = (managedBy === 'Kompaniya') ? 'block' : 'none';
        }

        function showUseFields(show) {
            document.getElementById('useFields').style.display = show ? 'block' : 'none';
        }
    </script>

    <style>
        .custom-radio {
            width: 20px;
            height: 20px;
            cursor: pointer;
            margin: 0px;
        }
    </style>
@endsection
@section('scripts')
    <!-- Include Google Maps JavaScript API with Places Library -->
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAAnUwWTguBMsDU8UrQ7Re-caVeYCmcHQY&libraries&libraries=places&callback=initMap"
        async defer></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> --}}

    <script>
        // Wrap the entire script in an IIFE to avoid global scope pollution
        (function() {
            let fileInputCount = 4;
            let activeFileInput = null;
            let videoStream = null;
            let map, marker, infoWindow;

            // Parse the aktivs data from the server-side variable
            let aktivs = @json($aktivs ?? []);

            // Wait for the DOM to be fully loaded
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize event listeners and other setup tasks
                initializeEventListeners();
                initializeFileInputs();
                validateFiles();
                initMap();
            });

            function initializeEventListeners() {
                // Camera modal elements
                const captureButton = document.getElementById('captureButton');
                const saveButton = document.getElementById('saveButton');
                const cameraPreview = document.getElementById('cameraPreview');
                const cameraError = document.getElementById('cameraError');

                // Form elements
                const addFileBtn = document.getElementById('add-file-btn');
                const submitBtn = document.getElementById('submit-btn');
                const aktivForm = document.getElementById('aktiv-form');

                // Map elements
                const findMyLocationBtn = document.getElementById('find-my-location');

                // Add event listeners if the elements exist
                if (captureButton) {
                    captureButton.addEventListener('click', capturePhoto);
                }

                if (saveButton) {
                    saveButton.addEventListener('click', savePhoto);
                }

                if (addFileBtn) {
                    addFileBtn.addEventListener('click', addFileInput);
                }

                if (aktivForm) {
                    aktivForm.addEventListener('submit', handleFormSubmit);
                }

                if (findMyLocationBtn) {
                    findMyLocationBtn.addEventListener('click', findMyLocation);
                }
            }

            function initializeFileInputs() {
                // Initialize existing file inputs
                for (let i = 1; i <= fileInputCount; i++) {
                    const fileInput = document.getElementById('file' + i);
                    if (fileInput) {
                        fileInput.addEventListener('change', validateFiles);
                    }
                }
            }

            function openCameraModal(fileInputId) {
                activeFileInput = document.getElementById(fileInputId);
                const cameraModal = new bootstrap.Modal(document.getElementById('cameraModal'), {});
                cameraModal.show();

                // Check for camera support
                if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                    navigator.mediaDevices
                        .getUserMedia({
                            video: {
                                facingMode: 'environment',
                            },
                        })
                        .then((stream) => {
                            videoStream = stream;
                            document.getElementById('cameraPreview').srcObject = stream;
                            document.getElementById('captureButton').disabled = false;
                            document.getElementById('cameraError').textContent = '';
                        })
                        .catch((error) => {
                            document.getElementById('cameraError').textContent =
                                'Камерага кириш мумкин эмас: ' + error.message;
                            document.getElementById('captureButton').disabled = true;
                        });
                } else {
                    document.getElementById('cameraError').textContent =
                        'Браузерингиз камерадан фойдаланишни қўллаб-қувватламайди.';
                    document.getElementById('captureButton').disabled = true;
                }
            }

            function capturePhoto() {
                const video = document.getElementById('cameraPreview');
                const canvas = document.getElementById('snapshotCanvas');
                const context = canvas.getContext('2d');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                context.drawImage(video, 0, 0, canvas.width, canvas.height);
                if (videoStream) {
                    videoStream.getTracks().forEach((track) => track.stop());
                }
                document.getElementById('saveButton').disabled = false;
            }

            function savePhoto() {
                const canvas = document.getElementById('snapshotCanvas');
                canvas.toBlob((blob) => {
                    const file = new File([blob], `snapshot-${Date.now()}.jpg`, {
                        type: 'image/jpeg',
                    });
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    if (activeFileInput) {
                        activeFileInput.files = dataTransfer.files;
                        validateFiles();
                    }
                });
                document.getElementById('cameraPreview').srcObject = null;
                document.getElementById('saveButton').disabled = true;
            }

            function addFileInput() {
                fileInputCount++;
                const container = document.getElementById('file-upload-container');
                const newDiv = document.createElement('div');
                newDiv.classList.add('mb-3');

                const label = document.createElement('label');
                label.textContent = `Қўшимча файл ${fileInputCount}`;

                const inputGroup = document.createElement('div');
                inputGroup.classList.add('input-group');

                const input = document.createElement('input');
                input.type = 'file';
                input.name = 'files[]';
                input.classList.add('form-control');
                input.accept = 'image/*';
                input.required = true;
                input.id = 'file' + fileInputCount;
                input.addEventListener('change', validateFiles);

                const button = document.createElement('button');
                button.type = 'button';
                button.classList.add('btn', 'btn-secondary');
                button.textContent = '📷';
                button.addEventListener('click', function() {
                    openCameraModal(input.id);
                });

                inputGroup.appendChild(input);
                inputGroup.appendChild(button);
                newDiv.appendChild(label);
                newDiv.appendChild(inputGroup);
                container.appendChild(newDiv);
                validateFiles();
            }

            function validateFiles() {
                const submitBtn = document.getElementById('submit-btn');
                const errorDiv = document.getElementById('file-error');
                const fileInputs = document.querySelectorAll('input[type="file"][name="files[]"]');
                let totalFiles = 0;

                fileInputs.forEach((input) => {
                    if (input.files.length > 0) {
                        totalFiles += input.files.length;
                    }
                });

                if (totalFiles < 4) {
                    let filesNeeded = 4 - totalFiles;
                    errorDiv.textContent =
                        filesNeeded === 4 ?
                        'Сиз ҳеч қандай файл юкламадингиз.' :
                        `Сиз яна ${filesNeeded} та файл юклашингиз керак.`;
                    submitBtn.disabled = true;
                } else {
                    errorDiv.textContent = '';
                    submitBtn.disabled = false;
                }
            }

            function handleFormSubmit(event) {
                validateFiles();
                const submitBtn = document.getElementById('submit-btn');
                if (submitBtn.disabled) {
                    event.preventDefault();
                } else {
                    submitBtn.disabled = true;
                    submitBtn.innerText = 'Юкланмоқда...';
                }
            }

            function findMyLocation() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            const userLocation = {
                                lat: position.coords.latitude,
                                lng: position.coords.longitude,
                            };

                            map.setCenter(userLocation);
                            map.setZoom(15);
                            placeMarker(userLocation);
                        },
                        function(error) {
                            alert('Жойлашувингиз аниқланмади: ' + error.message);
                        }
                    );
                } else {
                    alert('Жойлашувни аниқлаш браузерингиз томонидан қўлланилмайди.');
                }
            }

            function initMap() {
                const mapOptions = {
                    center: {
                        lat: 41.2995,
                        lng: 69.2401,
                    },
                    zoom: 10,
                };
                map = new google.maps.Map(document.getElementById('map'), mapOptions);
                infoWindow = new google.maps.InfoWindow();

                // Existing aktivs markers
                if (aktivs && aktivs.length > 0) {
                    aktivs.forEach((aktiv) => {
                        if (aktiv.latitude && aktiv.longitude) {
                            const position = {
                                lat: parseFloat(aktiv.latitude),
                                lng: parseFloat(aktiv.longitude),
                            };

                            const aktivMarker = new google.maps.Marker({
                                position: position,
                                map: map,
                                title: aktiv.object_name,
                                icon: 'https://maps.google.com/mapfiles/ms/icons/yellow-dot.png',
                            });

                            aktivMarker.addListener('click', function() {
                                openInfoWindow(aktiv, aktivMarker);
                            });
                        }
                    });
                }

                // Add click event to place custom marker
                map.addListener('click', function(event) {
                    placeMarker(event.latLng);
                });

                // If latitude and longitude are already set, place a marker
                const latInput = document.getElementById('latitude').value;
                const lngInput = document.getElementById('longitude').value;
                if (latInput && lngInput) {
                    const position = {
                        lat: parseFloat(latInput),
                        lng: parseFloat(lngInput),
                    };
                    placeMarker(position);
                    map.setCenter(position);
                    map.setZoom(15);
                }
            }

            function openInfoWindow(aktiv, marker) {
                const mainImagePath =
                    aktiv.files && aktiv.files.length > 0 ?
                    `/storage/${aktiv.files[0].path}` :
                    'https://cdn.dribbble.com/users/1651691/screenshots/5336717/404_v2.png';

                const contentString = `
              <div style="width:250px;">
                  <h5>${aktiv.object_name}</h5>
                  <img src="${mainImagePath}" alt="Marker Image" style="width:100%;height:auto;"/>
                  <p><strong>Балансда сақловчи:</strong> ${aktiv.balance_keeper || 'N/A'}</p>
                  <p><strong>Мўлжал:</strong> ${aktiv.location || 'N/A'}</p>
                  <p><strong>Ер майдони (кв.м):</strong> ${aktiv.land_area || 'N/A'}</p>
                  <p><strong>Бино майдони (кв.м):</strong> ${aktiv.building_area || 'N/A'}</p>
                  <p><strong>Газ:</strong> ${aktiv.gas || 'N/A'}</p>
                  <p><strong>Сув:</strong> ${aktiv.water || 'N/A'}</p>
                  <p><strong>Электр:</strong> ${aktiv.electricity || 'N/A'}</p>
                  <p><strong>Қўшимча маълумот:</strong> ${aktiv.additional_info || 'N/A'}</p>
                  <p><strong>Қарта:</strong> <a href="${aktiv.geolokatsiya || '#'}" target="_blank">${
              aktiv.geolokatsiya || 'N/A'
            }</a></p>
              </div>
            `;

                infoWindow.setContent(contentString);
                infoWindow.open(map, marker);
            }

            function placeMarker(location) {
                if (marker) {
                    marker.setMap(null);
                }
                marker = new google.maps.Marker({
                    position: location,
                    map: map,
                });

                const lat = typeof location.lat === 'function' ? location.lat() : location.lat;
                const lng = typeof location.lng === 'function' ? location.lng() : location.lng;

                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;
                document.getElementById('geolokatsiya').value = `https://www.google.com/maps?q=${lat},${lng}`;
            }

            // Expose initMap to the global scope for Google Maps callback
            window.initMap = initMap;
        })();
    </script>
@endsection
