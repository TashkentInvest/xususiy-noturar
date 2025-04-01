@extends('layouts.admin')

@section('content')
    <div class="container mt-5">
        <div class="card shadow-lg p-4 rounded-4 border-0">
            <h2 class="mb-4 text-center text-primary fw-bold">Ер Тўла Яратиш</h2>

            <form action="{{ route('yertola.store') }}" method="POST" enctype="multipart/form-data" id="yertola-form">
                @csrf

                <!-- Manzil tanlash -->
                <div class="mb-4">
                    <label class="form-label fw-bold">📍 Манзилни танланг:</label>

                    @include('inc.__address')

                    <div class="row">
                        <!-- Right Column -->
                        <div class="col-lg-12 col-md-12 col-12 mt-3">
                            <div class="mb-3">
                                <label class="text-danger">Файлларни юклаш (Камида 4 та расм мажбурий)</label>
                            </div>

                            <div id="fileInputsContainer" class="row">
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
                            </div>

                            <div id="file-error" class="text-danger mb-3"></div>
                            <div id="file-upload-container"></div>
                            <button type="button" class="btn btn-secondary mb-3" id="add-file-btn">Янги файл
                                қўшиш</button>
                        </div>
                        <div class="col-lg-12 col-md-12 col-12 mt-3">
                            <div class="mb-3">
                                <button id="find-my-location" type="button" class="btn btn-primary mb-3">Менинг
                                    жойлашувимни
                                    топиш</button>
                                <div id="map" style="height: 400px; width: 100%; border-radius: 10px;"></div>

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
                                value="1" onclick="showExtraFields(true)"
                                {{ old('does_exists_yer_tola') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold">✅ Мавжуд</label>
                        </div>
                        <div class="form-check ml-3">
                            <input class="form-check-input custom-radio" type="radio" name="does_exists_yer_tola"
                                value="0" onclick="showExtraFields(false)"
                                {{ old('does_exists_yer_tola') == '0' ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold">❌ Мавжуд эмас</label>
                        </div>
                    </div>
                </div>

                <!-- Агар мавжуд бўлса -->
                <div id="extraFields" class="mb-4 p-3 border rounded bg-light shadow-sm" style="display: none;">
                    <label class="form-label fw-bold">🔧 Бошқарув шакли:</label>
                    <select name="managed_by" id="managedBy" class="form-select form-control-lg shadow-sm"
                        onchange="toggleManagementFields()">
                        <option value="">Танланг</option>
                        <option value="Kompaniya" {{ old('managed_by') == 'Kompaniya' ? 'selected' : '' }}>🏢 Бошқарув сервис компанияси
                        </option>
                        <option value="O'z o'zini boshqaradi"
                            {{ old('managed_by') == "O'z o'zini boshqaradi" ? 'selected' : '' }}>👤 Ўз-ўзини бошқаради
                        </option>
                    </select>

                    <!-- Company Management Section (Shows when Kompaniya is selected) -->
                    <div id="companySection" class="mt-3" style="display: none;">
                        <div class="input-group mb-2">
                            <input type="text" name="company_search" id="companySearch"
                                class="form-control form-control-lg shadow-sm"
                                placeholder="🔍 Компания номи ёки СТИР рақами">
                            <button type="button" class="btn btn-primary" onclick="searchCompany()">
                                <i class="fas fa-search"></i> Излаш
                            </button>
                        </div>
                        <div id="searchResults" class="list-group mt-2" style="display: none;"></div>

                        <!-- Hidden field to store selected company_management_id -->
                        <input type="hidden" name="company_management_id" id="companyManagementId"
                            value="{{ old('company_management_id') }}">

                        <!-- Selected company display -->
                        <div id="selectedCompany" class="alert alert-success mt-2" style="display: none;">
                            <strong>Танланган бошқарув сервис компанияси:</strong> <span id="companyName"></span>
                            <button type="button" class="btn-close float-end" onclick="clearSelectedCompany()"></button>
                        </div>

                        <!-- Button to show modal for creating a new company -->
                        <button type="button" class="btn btn-outline-primary mt-2" onclick="showNewCompanyModal()">
                            ➕ Янги бошқарув сервис компаниясини яратиш
                        </button>
                    </div>

                    <!-- Self-management Section (Shows when O'z o'zini boshqaradi is selected) -->
                    <div id="selfManagementSection" class="mt-3" style="display: none;">
                        <input type="text" name="balance_keeper" class="form-control form-control-lg shadow-sm mb-2"
                            placeholder="🔹 Балансга масъул шахс" value="{{ old('balance_keeper') }}">
                    </div>

                    <!-- Фойдаланиш мумкинми? -->
                    <div class="mt-4">
                        <label class="form-label fw-bold">❓ Фойдаланиш мумкинми?</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input custom-radio" type="radio"
                                    name="does_can_we_use_yer_tola" value="1" onclick="showUseIjaraFields(true)"
                                    {{ old('does_can_we_use_yer_tola') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold">✅ Ҳа</label>
                            </div>
                            <div class="form-check ml-3">
                                <input class="form-check-input custom-radio" type="radio"
                                    name="does_can_we_use_yer_tola" value="0" onclick="showUseIjaraFields(false)"
                                    {{ old('does_can_we_use_yer_tola') == '0' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold">❌ Йўқ</label>
                            </div>
                        </div>
                    </div>

                    <!-- Ижарага бериш мумкинми? -->
                    <div id="UseIjaraFields" class="mt-4" style="display: none;">
                        <label class="form-label fw-bold">❓ Ижарага бериш мумкинми?</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input custom-radio" type="radio"
                                    name="does_yer_tola_ijaraga_berish_mumkin" value="1"
                                    onclick="showUseFields(true)"
                                    {{ old('does_yer_tola_ijaraga_berish_mumkin') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold">✅ Ҳа</label>
                            </div>
                            <div class="form-check ml-3">
                                <input class="form-check-input custom-radio" type="radio"
                                    name="does_yer_tola_ijaraga_berish_mumkin" value="0"
                                    onclick="showUseFields(false)"
                                    {{ old('does_yer_tola_ijaraga_berish_mumkin') == '0' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold">❌ Йўқ</label>
                            </div>
                        </div>
                    </div>
                    <!-- Агар фойдаланиш мумкин бўлса -->
                    <div id="useFields" class="mt-4" style="display: none;">
                        <input type="number" name="umumiy_maydoni_yer_tola"
                            class="form-control form-control-lg shadow-sm mb-2" placeholder="📏 Ертўланинг умумий майдони (м²)"
                            value="{{ old('ijaraga_berilgan_qismi_yer_tola') }}">
                        <input type="number" name="ijaraga_berilgan_qismi_yer_tola"
                            class="form-control form-control-lg shadow-sm mb-2"
                            placeholder="📏 Ижарага берилган қисм (м²)"
                            value="{{ old('ijaraga_berilgan_qismi_yer_tola') }}">
                        <input type="number" name="ijaraga_berilmagan_qismi_yer_tola"
                            class="form-control form-control-lg shadow-sm mb-2"
                            placeholder="📏 Ижарага берилмаган қисм (м²)"
                            value="{{ old('ijaraga_berilmagan_qismi_yer_tola') }}">
                        <input type="number" name="texnik_qismi_yer_tola" class="form-control form-control-lg shadow-sm"
                            placeholder="⚙ Техник қисм (м²)" value="{{ old('texnik_qismi_yer_tola') }}">

                        <!-- Ижара нархи -->
                        <div class="mb-3 mt-3">
                            <label class="form-label fw-bold">💰 Ижарага берилган қисмини ойлик ижара қиймати (сўм):</label>
                            <input type="number" name="oylik_ijara_narxi_yer_tola"
                                class="form-control form-control-lg shadow-sm" placeholder="💵 Сум"
                                value="{{ old('oylik_ijara_narxi_yer_tola') }}">
                        </div>

                        <!-- Фаолият тури (Checkbox) -->
                        @php
                            $faoliyatTurlari = [
                                'Gozallik Saloni' => '💄 Гўзаллик салони',
                                'Dorixona' => '💊 Дорихона',
                                'Kompyuter Xizmati' => '💻 Компьютер хизмати',
                                'Savdo' => '🛍 Савдо',
                            ];

                            // Additional values to ensure they are included
                            $additionalTurlari = [
                                'Oquv Markazi' => '📚 Ўқув маркази',
                                'Tikuvchilik' => '🧵 Тикувчилик',
                                'Kosibchilik' => '🪡 Косибчилик',
                                'Poligrafiya' => '🖨 Полиграфия',
                                'Fotostudiya' => '📸 Фотостудия',
                                'Kafe' => '☕️ Кафе',
                                'Maishiy Texnika Tamirlash' => '🔧 Маиший техника таъмирлаш устахонаси',
                                'Sartoroshxona' => '✂️ Сарторошхона',
                                'Ximchistka' => '🧼 Химчистка',
                                'Avtomoyka' => '🚗 Автомойка',
                                'Ofis' => '🏢 Офис',
                                'Boshqalar' => '🔹 Бошқалар',
                            ];

                            // Merge additional only if keys not already set
                            foreach ($additionalTurlari as $key => $value) {
                                if (!array_key_exists($key, $faoliyatTurlari)) {
                                    $faoliyatTurlari[$key] = $value;
                                }
                            }

                            $oldFaoliyatTuri = old('faoliyat_turi', []);
                        @endphp

                        <div class="mb-3">
                            <label class="form-label fw-bold">🏢 Фаолият тури:</label>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach ($faoliyatTurlari as $key => $value)
                                    <div class="form-check d-flex align-items-center ml-3">
                                        <input class="form-check-input m-0" type="checkbox" name="faoliyat_turi[]"
                                            value="{{ $key }}"
                                            {{ in_array($key, $oldFaoliyatTuri) ? 'checked' : '' }}>
                                        <label class="form-check-label">{{ $value }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Юбориш тугмаси -->
                <div class="text-center">
                    <button type="submit" id="submit-btn" class="btn btn-primary btn-lg px-5 shadow-sm fw-bold">💾
                        Сақлаш</button>
                </div>
            </form>
        </div>

        <!-- Modal for creating a new company -->
        <div class="modal fade" id="newCompanyModal" tabindex="-1" aria-labelledby="newCompanyModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="newCompanyModalLabel">🏢 Янги компания яратиш</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="modalOrganization" class="form-label">Ташкилот номи</label>
                            <input type="text" class="form-control" id="modalOrganization" name="modal_organization"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="modalInn" class="form-label">СТИР рақами</label>
                            <input type="text" class="form-control" id="modalInn" name="modal_inn" required>
                        </div>
                        <div class="mb-3">
                            <label for="modalRepresentative" class="form-label">Вакил</label>
                            <input type="text" class="form-control" id="modalRepresentative"
                                name="modal_representative">
                        </div>
                        <div class="mb-3">
                            <label for="modalPhone" class="form-label">Телефон</label>
                            <input type="text" class="form-control" id="modalPhone" name="modal_phone">
                        </div>
                        <div class="mb-3">
                            <label for="modalServicePhone" class="form-label">Хизмат телефони</label>
                            <input type="text" class="form-control" id="modalServicePhone"
                                name="modal_service_phone">
                        </div>
                        <div class="mb-3">
                            <label for="modalDistrict" class="form-label">Туман</label>
                            <input type="text" class="form-control" id="modalDistrict" name="modal_district">
                        </div>
                        <div class="mb-3">
                            <label for="modalAddress" class="form-label">Манзил</label>
                            <input type="text" class="form-control" id="modalAddress" name="modal_address">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Бекор қилиш</button>
                        <button type="button" class="btn btn-primary" onclick="createNewCompany()">Яратиш</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Camera Modal -->
        <div class="modal fade" id="cameraModal" tabindex="-1" aria-labelledby="cameraModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="cameraModalLabel">📸 Камера</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center">
                            <video id="cameraPreview" autoplay style="width: 100%; max-height: 400px;"></video>
                            <canvas id="snapshotCanvas" style="display: none;"></canvas>
                            <div id="cameraError" class="text-danger mt-2"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Бекор қилиш</button>
                        <button type="button" class="btn btn-info" id="captureButton" disabled>Расмга олиш</button>
                        <button type="button" class="btn btn-primary" id="saveButton" disabled>Сақлаш</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts for Dynamic Fields -->
    <script>
        function showExtraFields(show) {
            document.getElementById('extraFields').style.display = show ? 'block' : 'none';
        }

        function toggleManagementFields() {
            const managedBy = document.getElementById('managedBy').value;
            document.getElementById('companySection').style.display = (managedBy === 'Kompaniya') ? 'block' : 'none';
            document.getElementById('selfManagementSection').style.display = (managedBy === "O'z o'zini boshqaradi") ?
                'block' : 'none';

            // Clear any previous selections when changing management type
            if (managedBy !== 'Kompaniya') {
                clearSelectedCompany();
            }
        }

        function showUseIjaraFields(show) {
            document.getElementById('UseIjaraFields').style.display = show ? 'block' : 'none';
            // Hide useFields if not showing UseIjaraFields
            if (!show) {
                document.getElementById('useFields').style.display = 'none';
            }
        }

        function showUseFields(show) {
            document.getElementById('useFields').style.display = show ? 'block' : 'none';
        }

        function searchCompany() {
            const searchTerm = document.getElementById('companySearch').value.trim();
            if (searchTerm.length < 2) {
                alert('Камида 2 та белги киритинг');
                return;
            }

            // Show loading indicator
            document.getElementById('searchResults').innerHTML =
                '<div class="text-center"><div class="spinner-border text-primary" role="status"></div><p>Излаш...</p></div>';
            document.getElementById('searchResults').style.display = 'block';

            // Perform AJAX search
            fetch(`/api/company-management/search?q=${encodeURIComponent(searchTerm)}`, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Сервер хатоси');
                    }
                    return response.json();
                })
                .then(data => {
                    const resultsDiv = document.getElementById('searchResults');

                    if (data.length === 0) {
                        resultsDiv.innerHTML =
                            '<div class="alert alert-warning">Компания топилмади. Янги компания яратишингиз мумкин.</div>';
                        return;
                    }

                    let html = '';
                    data.forEach(company => {
                        // Escape values to prevent XSS
                        const escapedOrg = company.organization.replace(/"/g, '&quot;');
                        const escapedInn = company.inn ? company.inn.replace(/"/g, '&quot;') : '';
                        const escapedRep = company.representative ? company.representative.replace(/"/g,
                            '&quot;') : 'Вакил кўрсатилмаган';
                        const escapedPhone = company.phone ? company.phone.replace(/"/g, '&quot;') : 'N/A';

                        html += `<a href="#" class="list-group-item list-group-item-action"
                                onclick="selectCompany(${company.id}, '${escapedOrg}', '${escapedInn}')">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">${escapedOrg}</h5>
                                    <small>СТИР: ${escapedInn}</small>
                                </div>
                                <p class="mb-1">${escapedRep}</p>
                                <small>Тел: ${escapedPhone}</small>
                            </a>`;
                    });

                    resultsDiv.innerHTML = html;
                })
                .catch(error => {
                    console.error('Error searching for company:', error);
                    document.getElementById('searchResults').innerHTML =
                        `<div class="alert alert-danger">Хатолик юз берди: ${error.message}</div>`;
                });
        }

        function selectCompany(id, name, inn) {
            // Set the hidden input value
            document.getElementById('companyManagementId').value = id;

            // Update selected company display
            document.getElementById('companyName').textContent = `${name} (СТИР: ${inn})`;
            document.getElementById('selectedCompany').style.display = 'block';

            // Clear search results and search input
            document.getElementById('searchResults').style.display = 'none';
            document.getElementById('companySearch').value = '';
        }

        function clearSelectedCompany() {
            document.getElementById('companyManagementId').value = '';
            document.getElementById('selectedCompany').style.display = 'none';
            document.getElementById('companyName').textContent = '';
        }

        function showNewCompanyModal() {
            // Clear any previous values
            document.getElementById('modalOrganization').value = '';
            document.getElementById('modalInn').value = '';
            document.getElementById('modalRepresentative').value = '';
            document.getElementById('modalPhone').value = '';
            document.getElementById('modalServicePhone').value = '';
            document.getElementById('modalDistrict').value = '';
            document.getElementById('modalAddress').value = '';

            // Get the search term and use it if available
            const searchTerm = document.getElementById('companySearch').value.trim();
            if (searchTerm) {
                // If search term looks like an INN (numeric, typically 9 digits)
                if (/^\d+$/.test(searchTerm)) {
                    document.getElementById('modalInn').value = searchTerm;
                } else {
                    document.getElementById('modalOrganization').value = searchTerm;
                }
            }

            // Show the modal
            const modal = new bootstrap.Modal(document.getElementById('newCompanyModal'));
            modal.show();
        }

        function createNewCompany() {
            const organization = document.getElementById('modalOrganization').value.trim();
            const inn = document.getElementById('modalInn').value.trim();

            if (!organization || !inn) {
                alert('Ташкилот номи ва СТИР рақами мажбурий');
                return;
            }

            // Collect form data
            const formData = {
                organization: organization,
                inn: inn,
                representative: document.getElementById('modalRepresentative').value.trim(),
                phone: document.getElementById('modalPhone').value.trim(),
                service_phone: document.getElementById('modalServicePhone').value.trim(),
                district: document.getElementById('modalDistrict').value.trim(),
                address: document.getElementById('modalAddress').value.trim()
            };

            // Show loading spinner
            const submitBtn = document.querySelector('#newCompanyModal .btn-primary');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.innerHTML =
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Юкланмоқда...';
            submitBtn.disabled = true;

            // Create new company via AJAX
            fetch('/api/company-management/create', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw new Error(data.error || 'Хатолик юз берди');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    // Close the modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('newCompanyModal'));
                    modal.hide();

                    // Select the newly created company
                    selectCompany(data.id, data.organization, data.inn);

                    // Show success message
                    alert('Компания муваффақиятли яратилди!');
                })
                .catch(error => {
                    console.error('Error creating company:', error);
                    alert('Хатолик юз берди: ' + error.message);
                })
                .finally(() => {
                    // Reset button state
                    submitBtn.innerHTML = originalBtnText;
                    submitBtn.disabled = false;
                });
        }
    </script>

    <style>
        .custom-radio {
            width: 20px;
            height: 20px;
            cursor: pointer;
            margin: 0px;
        }

        #searchResults {
            max-height: 300px;
            overflow-y: auto;
        }

        .alert-success {
            position: relative;
            padding-right: 40px;
        }

        .btn-close {
            font-size: 0.8rem;
        }
    </style>
@endsection

@section('scripts')
    <!-- Include Google Maps JavaScript API with Places Library -->
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAAnUwWTguBMsDU8UrQ7Re-caVeYCmcHQY&libraries=places&callback=initMap"
        async defer></script>

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

                // Initialize form state from old input
                initializeFormState();
            });

            function initializeFormState() {
                // Initialize the visibility of conditional sections based on old input values
                const doesExistYerTola = document.querySelector('input[name="does_exists_yer_tola"]:checked');
                if (doesExistYerTola) {
                    showExtraFields(doesExistYerTola.value === '1');
                }

                const doesCanUseYerTola = document.querySelector('input[name="does_can_we_use_yer_tola"]:checked');
                if (doesCanUseYerTola) {
                    showUseIjaraFields(doesCanUseYerTola.value === '1');
                }

                const doesYerTolaIjaragaBerishMumkin = document.querySelector(
                    'input[name="does_yer_tola_ijaraga_berish_mumkin"]:checked');
                if (doesYerTolaIjaragaBerishMumkin) {
                    showUseFields(doesYerTolaIjaragaBerishMumkin.value === '1');
                }

                // Initialize management fields
                toggleManagementFields();

                // If company_management_id is set, try to load company info
                const companyManagementId = document.getElementById('companyManagementId').value;
                if (companyManagementId) {
                    loadCompanyInfo(companyManagementId);
                }
            }

            function loadCompanyInfo(companyId) {
                if (!companyId) return;

                fetch(`/api/company-management/${companyId}`, {
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Компания маълумотларини юклашда хатолик юз берди');
                        }
                        return response.json();
                    })
                    .then(company => {
                        if (company && company.id) {
                            selectCompany(company.id, company.organization, company.inn);
                        }
                    })
                    .catch(error => {
                        console.error('Error loading company info:', error);
                    });
            }

            function initializeEventListeners() {
                // Camera modal elements
                const captureButton = document.getElementById('captureButton');
                const saveButton = document.getElementById('saveButton');
                const cameraPreview = document.getElementById('cameraPreview');
                const cameraError = document.getElementById('cameraError');

                // Form elements
                const addFileBtn = document.getElementById('add-file-btn');
                const submitBtn = document.getElementById('submit-btn');
                const yerTolaForm = document.getElementById('yertola-form');

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

                if (yerTolaForm) {
                    yerTolaForm.addEventListener('submit', handleFormSubmit);
                }

                if (findMyLocationBtn) {
                    findMyLocationBtn.addEventListener('click', findMyLocation);
                }

                // Add keypress event for company search
                const companySearch = document.getElementById('companySearch');
                if (companySearch) {
                    companySearch.addEventListener('keypress', function(e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            searchCompany();
                        }
                    });
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

                // Close the modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('cameraModal'));
                modal.hide();
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
                    alert('Илтимос, камида 4 та расм юкланг!');
                } else {
                    // Check company management if needed
                    const managedBy = document.getElementById('managedBy').value;
                    if (managedBy === 'Kompaniya') {
                        const companyId = document.getElementById('companyManagementId').value;
                        if (!companyId) {
                            event.preventDefault();
                            alert('Илтимос, бошқарув компаниясини танланг ёки янгисини яратинг!');
                            return;
                        }
                    }

                    // Form is valid, proceed with submission
                    submitBtn.disabled = true;
                    submitBtn.innerHTML =
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Юкланмоқда...';
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
                      <h5>${aktiv.object_name || 'Ер тўла'}</h5>
                      <img src="${mainImagePath}" alt="Marker Image" style="width:100%;height:auto;"/>
                      <p><strong>Балансда сақловчи:</strong> ${aktiv.balance_keeper || 'N/A'}</p>
                      <p><strong>Мўлжал:</strong> ${aktiv.location || 'N/A'}</p>
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
