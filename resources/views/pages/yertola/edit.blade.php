@extends('layouts.admin')

@section('content')
    <div class="container mt-5">
        <div class="card shadow-lg p-4 rounded-4 border-0">
            <h2 class="mb-4 text-center text-primary fw-bold">Ер Тўла Таҳрирлаш</h2>

            <form action="{{ route('yertola.update', $yertola->id ?? 1) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Manzil tanlash -->
                <div class="mb-4">
                    <label class="form-label fw-bold">📍 Манзилни танланг:</label>
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5>Манзилни озгартириш</h5>
                        </div>
                        <div class="card-body row">
                            <div class="col-lg-6 col-md-12 col-12 mb-3">
                                <label for="region_id">Худуд</label>
                                <select class="form-control region_id select2" name="region_id" id="region_id" required>
                                    <option value="" disabled selected>Худудни танланг</option>
                                    @foreach ($regions as $region)
                                        <option value="{{ $region->id }}"
                                            {{ $region->id == old('region_id', optional($yertola->subStreet->district->region)->id) ? 'selected' : '' }}>
                                            {{ $region->name_uz }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-6 col-md-12 col-12 mb-3">
                                <label for="district_id">Туман</label>
                                <select class="form-control district_id select2" name="district_id" id="district_id"
                                    required>
                                    <option value="" disabled selected>Туманни танланг</option>
                                    @foreach ($districts as $district)
                                        <option value="{{ $district->id }}"
                                            {{ $district->id == old('district_id', optional($yertola->subStreet->district)->id) ? 'selected' : '' }}>
                                            {{ $district->name_uz }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-6 col-md-12 col-12 mb-3">
                                <label for="street_id" class="me-2">Мфй (Majburiy)<span></span></label>
                                <div class="d-flex align-items-end">
                                    <select class="form-control street_id select2" name="street_id" id="street_id" required>
                                        <option value="" disabled selected>Мфй ни танланг</option>
                                        @foreach ($streets as $street)
                                            <option value="{{ $street->id }}"
                                                {{ $street->id == old('street_id', $yertola->street_id) ? 'selected' : '' }}>
                                                {{ $street->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-primary ms-2" id="add_street_btn"
                                        title="Мфй қошиш">+</button>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-12 col-12 mb-3">
                                <label for="substreet_id" class="me-2">Кўча (Majburiy)<span></span></label>
                                <div class="d-flex align-items-end">
                                    <select class="form-control sub_street_id select2" name="sub_street_id"
                                        id="substreet_id" required>
                                        <option value="" disabled selected>Кўчани танланг</option>
                                        @foreach ($substreets as $substreet)
                                            <option value="{{ $substreet->id }}"
                                                {{ $substreet->id == old('sub_street_id', $yertola->sub_street_id) ? 'selected' : '' }}>
                                                {{ $substreet->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-primary ms-2" id="add_substreet_btn"
                                        title="Кўча қошиш">+</button>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="home_number" class="me-2">Уй рақами (Мажбурий эмас)</label>
                                    <div class="d-flex align-items-end">
                                        <input class="form-control" name="home_number" type="text" id="home_number"
                                            value="{{ old('home_number', $yertola->home_number) }}" />
                                    </div>
                                    <span class="text-danger error-message" id="home_number_error"></span>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="apartment_number" class="me-2">Квартира рақами (Мажбурий
                                        эмас)</label>
                                    <div class="d-flex align-items-end">
                                        <input class="form-control" name="apartment_number" type="text"
                                            value="{{ old('apartment_number', $yertola->apartment_number) }}"
                                            id="apartment_number" />
                                    </div>
                                    <span class="text-danger error-message" id="apartment_number_error"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <style>
                        .select2 {
                            width: 100% !important;
                        }
                    </style>

                    <script>
                        $(document).ready(function() {
                            $('.select2').select2();

                            function fetchDistricts(regionId, selectedDistrictId = null) {
                                $.ajax({
                                    url: "{{ route('getDistricts') }}",
                                    type: "GET",
                                    data: {
                                        region_id: regionId
                                    },
                                    success: function(data) {
                                        $('.district_id').empty().append(
                                            '<option value="" disabled selected>Туманни танланг</option>');
                                        $.each(data, function(key, value) {
                                            $('.district_id').append('<option value="' + key + '">' + value +
                                                '</option>');
                                        });
                                        if (selectedDistrictId) {
                                            $('.district_id').val(selectedDistrictId).trigger('change');
                                        }
                                    },
                                    error: function(xhr, status, error) {
                                        console.error('Error fetching District:', error);
                                    }
                                });
                            }

                            function fetchStreets(districtId, selectedStreetId = null) {
                                $.ajax({
                                    url: "{{ route('getStreets') }}",
                                    type: "GET",
                                    data: {
                                        district_id: districtId
                                    },
                                    success: function(data) {
                                        $('.street_id').empty().append(
                                            '<option value="" disabled selected>Мфй ни танланг</option>');
                                        $.each(data, function(key, value) {
                                            $('.street_id').append('<option value="' + key + '">' + value +
                                                '</option>');
                                        });
                                        if (selectedStreetId) {
                                            setTimeout(function() {
                                                $('.street_id').val(selectedStreetId).trigger('change');
                                            }, 500); // Adding a delay to ensure the data is fully loaded
                                        }
                                    },
                                    error: function(xhr, status, error) {
                                        console.error('Error fetching streets:', error);
                                    }
                                });
                            }

                            function fetchSubStreets(districtId, selectedSubStreetId = null) {
                                $.ajax({
                                    url: "{{ route('getSubStreets') }}",
                                    type: "GET",
                                    data: {
                                        district_id: districtId
                                    },
                                    success: function(data) {
                                        $('.sub_street_id').empty().append(
                                            '<option value="" disabled selected>Кўчани танланг</option>');
                                        $.each(data, function(key, value) {
                                            $('.sub_street_id').append('<option value="' + key + '">' + value +
                                                '</option>');
                                        });
                                        if (selectedSubStreetId) {
                                            setTimeout(function() {
                                                $('.sub_street_id').val(selectedSubStreetId).trigger('change');
                                            }, 500); // Adding a delay to ensure the data is fully loaded
                                        }
                                    },
                                    error: function(xhr, status, error) {
                                        console.error('Error fetching substreets:', error);
                                    }
                                });
                            }

                            // Initialize selections if data exists
                            // var selectedRegionId = "{{ old('region_id', optional($yertola->subStreet->district->region)->id) }}";
                            // var selectedDistrictId = "{{ old('district_id', optional($yertola->subStreet->district)->id) }}";
                            // var selectedStreetId = "{{ old('street_id', $yertola->street_id) }}";
                            // var selectedSubStreetId = "{{ old('sub_street_id', $yertola->sub_street_id) }}";

                            // if (selectedRegionId) {
                            //     fetchDistricts(selectedRegionId, selectedDistrictId);
                            // }
                            // if (selectedDistrictId) {
                            //     fetchStreets(selectedDistrictId, selectedStreetId);
                            // }
                            // if (selectedDistrictId) {
                            //     fetchSubStreets(selectedDistrictId, selectedSubStreetId);
                            // }

                            // Update Districts based on Region change
                            $('.region_id').change(function() {
                                var regionId = $(this).val();
                                fetchDistricts(regionId);
                            });

                            // Update Streets and SubStreets based on District change
                            $('.district_id').change(function() {
                                var districtId = $(this).val();
                                fetchStreets(districtId);
                                fetchSubStreets(districtId);
                            });

                            // Add Street Button Click Event
                            $('#add_street_btn').click(function() {
                                var districtId = $('#district_id').val();
                                if (!districtId) {
                                    alert('Выберите район сначала');
                                    return;
                                }
                                var newStreetName = prompt('Введите название новой улицы:');
                                if (newStreetName) {
                                    $.ajax({
                                        url: "{{ route('create.streets') }}",
                                        type: "POST",
                                        data: {
                                            _token: '{{ csrf_token() }}',
                                            district_id: districtId,
                                            street_name: newStreetName
                                        },
                                        success: function(response) {
                                            $('.street_id').append('<option value="' + response.id + '">' +
                                                response.name + '</option>');
                                            $('.street_id').val(response.id).trigger('change');
                                            alert('Улица успешно добавлена: ' + response.name);
                                        },
                                        error: function(xhr, status, error) {
                                            console.error('Error adding street:', error);
                                            alert('Ошибка при добавлении улицы. Пожалуйста, попробуйте снова.');
                                        }
                                    });
                                }
                            });

                            // Add SubStreet Button Click Event
                            $('#add_substreet_btn').click(function() {
                                var districtId = $('#district_id').val();
                                if (!districtId) {
                                    alert('Выберите район сначала');
                                    return;
                                }
                                var newSubStreetName = prompt('Введите название новой подулицы:');
                                if (newSubStreetName) {
                                    $.ajax({
                                        url: "{{ route('create.substreets') }}",
                                        type: "POST",
                                        data: {
                                            _token: '{{ csrf_token() }}',
                                            district_id: districtId,
                                            sub_street_name: newSubStreetName
                                        },
                                        success: function(response) {
                                            $('.sub_street_id').append('<option value="' + response.id + '">' +
                                                response.name + '</option>');
                                            $('.sub_street_id').val(response.id);
                                            alert('Подулица успешно добавлена: ' + response.name);
                                        },
                                        error: function(xhr, status, error) {
                                            console.error('Error adding substreet:', error);
                                            alert(
                                                'Ошибка при добавлении подулицы. Пожалуйста, попробуйте снова.'
                                            );
                                        }
                                    });
                                }
                            });
                        });
                    </script>

                    <div class="row">
                        <!-- Right Column -->
                        <div class="col-lg-12 col-md-12 col-12 mt-3">
                            <div class="mb-3">
                                <label class="text-danger">Файлларни юклаш (Камида 4 та расм мажбурий)</label>
                            </div>

                            <div id="fileInputsContainer" class="row">
                                @if ($yertola->images && count($yertola->images) > 0)
                                    @foreach ($yertola->images as $index => $image)
                                        <div class="mb-3 col-lg-3 col-md-6 col-12" id="fileInput{{ $index + 1 }}">
                                            <label for="file{{ $index + 1 }}">Файл {{ $index + 1 }}</label>
                                            <div class="input-group">
                                                <div class="form-control overflow-hidden position-relative">
                                                    <img src="{{ asset('storage/' . $image) }}" alt="Мавжуд расм"
                                                        class="img-thumbnail" style="height: 40px;">
                                                    <input type="hidden" name="existing_files[]"
                                                        value="{{ $image }}">
                                                </div>
                                                <input type="file" class="form-control d-none" name="files[]"
                                                    id="file{{ $index + 1 }}" accept="image/*">
                                                <button type="button" class="btn btn-danger"
                                                    onclick="removeExistingFile(this)">❌</button>
                                                <button type="button" class="btn btn-secondary"
                                                    onclick="openCameraModal('file{{ $index + 1 }}')">📷</button>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    @for ($i = 1; $i <= 4; $i++)
                                        <div class="mb-3 col-lg-3 col-md-6 col-12" id="fileInput{{ $i }}">
                                            <label for="file{{ $i }}">Файл {{ $i }}</label>
                                            <div class="input-group">
                                                <input type="file" class="form-control" name="files[]"
                                                    id="file{{ $i }}" accept="image/*" required>
                                                <button type="button" class="btn btn-secondary"
                                                    onclick="openCameraModal('file{{ $i }}')">📷</button>
                                            </div>
                                        </div>
                                    @endfor
                                @endif
                            </div>

                            <div id="file-error" class="text-danger mb-3"></div>
                            <div id="file-upload-container"></div>
                            <button type="button" class="btn btn-secondary mb-3" id="add-file-btn">Янги файл
                                қўшиш</button>
                        </div>

                        <div class="col-lg-12 col-md-12 col-12 mt-3">
                            <div class="mb-3">
                                <button id="find-my-location" type="button" class="btn btn-primary mb-3">Менинг
                                    жойлашувимни топиш</button>
                                <div id="map" style="height: 400px; width: 100%; border-radius: 10px;"></div>

                                @error('latitude')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                                @error('longitude')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <input type="hidden" name="latitude" id="latitude"
                                value="{{ old('latitude', $yertola->latitude) }}">
                            <input type="hidden" name="longitude" id="longitude"
                                value="{{ old('longitude', $yertola->longitude) }}">
                            <div class="mb-3">
                                <label for="geolokatsiya">Геолокация (координата)</label>
                                <input class="form-control" type="text" name="geolokatsiya" id="geolokatsiya"
                                    readonly required value="{{ old('geolokatsiya', $yertola->geolokatsiya) }}">
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
                                value="1" onclick="showExtraFields(true)"
                                {{ $yertola->does_exists_yer_tola ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold">✅ Мавжуд</label>
                        </div>
                        <div class="form-check ml-3">
                            <input class="form-check-input custom-radio" type="radio" name="does_exists_yer_tola"
                                value="0" onclick="showExtraFields(false)"
                                {{ !$yertola->does_exists_yer_tola ? 'checked' : '' }}>
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
                        <option value="Kompaniya" {{ $yertola->managed_by == 'Kompaniya' ? 'selected' : '' }}>🏢 Компания
                        </option>
                        <option value="O'z o'zini boshqaradi"
                            {{ $yertola->managed_by == 'O\'z o\'zini boshqaradi' ? 'selected' : '' }}>👤 Ўз-ўзини бошқаради
                        </option>
                    </select>

                    <div class="mt-3">
                        <input type="text" name="balance_keeper" class="form-control form-control-lg shadow-sm mb-2"
                            placeholder="🔹 Балансга масъул шахс"
                            value="{{ old('balance_keeper', $yertola->balance_keeper) }}">
                        <input type="text" name="stir" id="stirField"
                            class="form-control form-control-lg shadow-sm" placeholder="📊 СТИР рақами"
                            value="{{ old('stir', $yertola->stir) }}" style="display: none;">
                    </div>

                    <!-- Фойдаланиш мумкинми? -->
                    <div class="mt-4">
                        <label class="form-label fw-bold">❓ Фойдаланиш мумкинми?</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input custom-radio" type="radio"
                                    name="does_can_we_use_yer_tola" value="1" onclick="showUseFields(true)"
                                    {{ $yertola->does_can_we_use_yer_tola ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold">✅ Ҳа</label>
                            </div>
                            <div class="form-check ml-3">
                                <input class="form-check-input custom-radio" type="radio"
                                    name="does_can_we_use_yer_tola" value="0" onclick="showUseFields(false)"
                                    {{ !$yertola->does_can_we_use_yer_tola ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold">❌ Йўқ</label>
                            </div>
                        </div>
                    </div>

                    <!-- Агар фойдаланиш мумкин бўлса -->
                    <div id="useFields" class="mt-4" style="display: none;">
                        <input type="number" name="ijaraga_berilgan_qismi_yer_tola"
                            class="form-control form-control-lg shadow-sm mb-2"
                            placeholder="📏 Ижарага берилган қисм (м²)"
                            value="{{ old('ijaraga_berilgan_qismi_yer_tola', $yertola->ijaraga_berilgan_qismi_yer_tola) }}">
                        <input type="number" name="ijaraga_berilмаган_qismi_yer_tola"
                            class="form-control form-control-lg shadow-sm mb-2"
                            placeholder="📏 Ижарага берилмаган қисм (м²)"
                            value="{{ old('ijaraga_berilмаган_qismi_yer_tola', $yertola->ijaraga_berilмаган_qismi_yer_tola) }}">
                        <input type="number" name="texnik_qismi_yer_tola" class="form-control form-control-lg shadow-sm"
                            placeholder="⚙ Техник қисм (м²)"
                            value="{{ old('texnik_qismi_yer_tola', $yertola->texnik_qismi_yer_tola) }}">

                        <!-- Ижара нархи -->
                        <div class="mb-3 mt-3">
                            <label class="form-label fw-bold">💰 Ойлик ижара нархи:</label>
                            <input type="number" name="oylik_ijara_narxi_yer_tola"
                                class="form-control form-control-lg shadow-sm" placeholder="💵 Сум"
                                value="{{ old('oylik_ijara_narxi_yer_tola', $yertola->oylik_ijara_narxi_yer_tola) }}">
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
                                    $selectedFaoliyatTurlari = $yertola->faoliyat_turi
                                        ? json_decode($yertola->faoliyat_turi, true)
                                        : [];
                                @endphp
                                @foreach ($faoliyatTurlari as $key => $value)
                                    <div class="form-check d-flex align-items-center ml-3">
                                        <input class="form-check-input m-0" type="checkbox" name="faoliyat_turi[]"
                                            value="{{ $key }}"
                                            {{ in_array($key, $selectedFaoliyatTurlari) ? 'checked' : '' }}>
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
        // Initialize form state on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Set initial states for conditional fields
            const doesExistYerTola = {{ $yertola->does_exists_yer_tola ? 'true' : 'false' }};
            const doesCanWeUseYerTola = {{ $yertola->does_can_we_use_yer_tola ? 'true' : 'false' }};
            const managedBy = "{{ $yertola->managed_by }}";

            // Show/hide extra fields based on existing data
            showExtraFields(doesExistYerTola);
            if (doesExistYerTola) {
                showUseFields(doesCanWeUseYerTola);
                document.getElementById('stirField').style.display = (managedBy === 'Kompaniya') ? 'block' : 'none';
            }

            // Initialize map with existing coordinates
            initMap();
        });

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

        function removeExistingFile(button) {
            const parent = button.closest('.input-group');
            const fileInput = parent.querySelector('input[type="file"]');

            // Remove the hidden field with the existing file path
            parent.querySelector('input[type="hidden"]').remove();

            // Show the file input and make it required
            fileInput.classList.remove('d-none');
            fileInput.setAttribute('required', 'required');

            // Remove the image thumbnail
            parent.querySelector('img').remove();

            // Remove the button itself
            button.remove();
        }

        // Map initialization function
        function initMap() {
            // This would depend on how your map is implemented in the original code
            // If you're using a library like Leaflet or Google Maps, you'd initialize it here
            // with the existing coordinates from the hidden fields

            const latitude = parseFloat(document.getElementById('latitude').value) || null;
            const longitude = parseFloat(document.getElementById('longitude').value) || null;

            if (latitude && longitude) {
                // Set the map center to the existing coordinates
                // This is a placeholder - replace with your actual map initialization code
                console.log("Map should center on:", latitude, longitude);

                // Update the geolocation display field
                document.getElementById('geolokatsiya').value = `${latitude}, ${longitude}`;
            }
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
    <script>
        // Function to toggle the required attribute on the kadastr_raqami input
        function toggleKadastrRequired() {
            var buildingType = document.getElementById('building_type').value;
            var kadastrInput = document.getElementById('kadastr_raqami');

            if (buildingType !== 'yer') {
                kadastrInput.setAttribute('required', 'required');
            } else {
                kadastrInput.removeAttribute('required');
            }
        }

        // Listen for changes in the building_type dropdown
        document.getElementById('building_type').addEventListener('change', toggleKadastrRequired);

        // Call the function once to set the initial state based on the current selection
        toggleKadastrRequired();
    </script>

    <!-- Include Google Maps script and initialization code -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAAnUwWTguBMsDU8UrQ7Re-caVeYCmcHQY&libraries=geometry">
    </script>
    <!-- Place the JavaScript code at the end, inside the 'scripts' section -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // JavaScript code goes here
            function validateFiles() {
                const submitBtn = document.getElementById('submit-btn');
                const errorDiv = document.getElementById('file-error');

                // Get all new file inputs
                const fileInputs = document.querySelectorAll('input[type="file"][name="files[]"]');

                let totalFiles = 0;
                fileInputs.forEach(input => {
                    totalFiles += input.files.length;
                });

                // Get the count of existing files not marked for deletion
                const existingFiles = document.querySelectorAll('#existing-files .existing-file');
                const deleteCheckboxes = document.querySelectorAll(
                    'input[type="checkbox"][name="delete_files[]"]:checked');
                const existingFilesCount = existingFiles.length - deleteCheckboxes.length;

                const totalFileCount = totalFiles + existingFilesCount;

                // Validate minimum file requirement
                if (totalFileCount < 4) {
                    let filesNeeded = 4 - totalFileCount;
                    if (totalFileCount === 0) {
                        errorDiv.textContent = 'Сиз ҳеч қандай файл мавжуд эмас. Илтимос, камида 4 та файл юкланг.';
                    } else {
                        errorDiv.textContent = 'Сиз яна ' + filesNeeded + ' та файл қўшишингиз керак.';
                    }
                    submitBtn.disabled = true;
                } else {
                    errorDiv.textContent = '';
                    submitBtn.disabled = false;
                }
            }

            function addFileInput() {
                const container = document.getElementById('file-upload-container');
                const fileInputCount = container.querySelectorAll('input[type="file"]').length + 1;
                const newDiv = document.createElement('div');
                newDiv.classList.add('mb-3');
                const label = document.createElement('label');
                label.textContent = 'Қўшимча файл ' + fileInputCount;
                const input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('name', 'files[]');
                input.setAttribute('class', 'form-control');
                input.addEventListener('change', validateFiles);
                newDiv.appendChild(label);
                newDiv.appendChild(input);
                container.appendChild(newDiv);
            }

            // Disable submit button initially
            document.getElementById('submit-btn').disabled = false; // Allow initial load if existing files >= 4

            // Add event listeners to initial file inputs
            document.getElementById('file1').addEventListener('change', validateFiles);
            document.getElementById('file2').addEventListener('change', validateFiles);
            document.getElementById('file3').addEventListener('change', validateFiles);
            document.getElementById('file4').addEventListener('change', validateFiles);

            // Add event listener to delete checkboxes
            const deleteCheckboxes = document.querySelectorAll('input[type="checkbox"][name="delete_files[]"]');
            deleteCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', validateFiles);
            });

            // Initial validation
            validateFiles();

            // Form submission handling
            document.getElementById('aktiv-form').addEventListener('submit', function(event) {
                // Re-validate files on submit
                validateFiles();

                // If the submit button is disabled, prevent form submission
                if (document.getElementById('submit-btn').disabled) {
                    event.preventDefault();
                } else {
                    document.getElementById('submit-btn').disabled = true;
                    document.getElementById('submit-btn').innerText = 'Юкланмоқда...';
                }
            });

            // Google Maps initialization
            let map;
            let marker;

            function initMap() {
                const mapOptions = {
                    center: {
                        lat: parseFloat(document.getElementById('latitude').value) || 41.2995,
                        lng: parseFloat(document.getElementById('longitude').value) || 69.2401
                    },
                    zoom: 10,
                };

                map = new google.maps.Map(document.getElementById('map'), mapOptions);

                if (document.getElementById('latitude').value && document.getElementById('longitude').value) {
                    const position = {
                        lat: parseFloat(document.getElementById('latitude').value),
                        lng: parseFloat(document.getElementById('longitude').value)
                    };
                    placeMarker(position);
                    map.setCenter(position);
                    map.setZoom(15);
                }

                document.getElementById('find-my-location').addEventListener('click', function() {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            function(position) {
                                const userLocation = {
                                    lat: position.coords.latitude,
                                    lng: position.coords.longitude
                                };

                                map.setCenter(userLocation);
                                map.setZoom(15);
                                placeMarker(userLocation);

                                // Set latitude, longitude, and geolocation URL in the input fields
                                document.getElementById('latitude').value = userLocation.lat;
                                document.getElementById('longitude').value = userLocation.lng;
                                document.getElementById('geolokatsiya').value =
                                    `https://www.google.com/maps?q=${userLocation.lat},${userLocation.lng}`;
                            },
                            function(error) {
                                console.error('Error occurred. Error code: ' + error.code);
                                alert('Жойлашувингиз аниқланмади: ' + error.message);
                            }
                        );
                    } else {
                        alert('Жойлашувни аниқлаш браузерингиз томонидан қўлланилмайди.');
                    }
                });

                map.addListener('click', function(event) {
                    placeMarker(event.latLng);
                });
            }

            function placeMarker(location) {
                if (marker) {
                    marker.setMap(null);
                }

                marker = new google.maps.Marker({
                    position: location,
                    map: map
                });

                const lat = typeof location.lat === "function" ? location.lat() : location.lat;
                const lng = typeof location.lng === "function" ? location.lng() : location.lng;

                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;
                document.getElementById('geolokatsiya').value = `https://www.google.com/maps?q=${lat},${lng}`;
            }

            // Initialize the map after the page has loaded
            initMap();
        });
    </script>
@endsection
