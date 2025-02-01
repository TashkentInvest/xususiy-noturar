@extends('layouts.admin')

@section('content')
    <h1>Янги Актив Яратиш</h1>

    <!-- Camera Modal -->
    <div class="modal fade" id="cameraModal" tabindex="-1" aria-labelledby="cameraModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Расм олиш</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Ёпиш"></button>
                </div>
                <div class="modal-body">
                    <video id="cameraPreview" width="100%" autoplay></video>
                    <canvas id="snapshotCanvas" style="display:none;"></canvas>
                    <div id="cameraError" class="text-danger"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="captureButton" disabled>Расм олиш</button>
                    <button type="button" class="btn btn-primary" id="saveButton" data-bs-dismiss="modal"
                        disabled>Сақлаш</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Form -->
    <form method="POST" action="{{ route('aktivs.store') }}" enctype="multipart/form-data" id="aktiv-form">
        @csrf
        <input type="hidden" name="user_id" value="{{ auth()->user()->id ?? 1 }}">
        <div class="row my-3">
            <!-- Left Column -->
            <div class="col-md-6">
                <!-- Form Inputs -->
                <div class="mb-3">

                <label for="working_24_7">24/7 режимда ишлайдими?</label>
                <select name="working_24_7" class="form-control" required>
                    <option value="" selected>Tanlang</option>
                    <option value="1" {{ old('working_24_7', $aktiv->working_24_7 ?? '') == '1' ? 'selected' : '' }}>
                        Ҳа</option>
                    <option value="0" {{ old('working_24_7', $aktiv->working_24_7 ?? '') == '0' ? 'selected' : '' }}>
                        Йўқ</option>
                </select>
                </div>
                <div class="mb-3">
                    <label for="faoliyat_xolati">Фаолият ҳолати</label>
                    <select name="faoliyat_xolati" id="faoliyat_xolati" class="form-control">
                        <option value="">Танланг</option>
                        <option value="work" {{ old('faoliyat_xolati') == 'work' ? 'selected' : '' }}>Ишламоқда</option>
                        <option value="notwork" {{ old('faoliyat_xolati') == 'notwork' ? 'selected' : '' }}>Ишламаяпти
                        </option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="object_name">Объект номи</label>
                    <input class="form-control" type="text" name="object_name" id="object_name"
                        placeholder="футбол майдони | 4 қаватли уйнинг 1-қавати" value="{{ old('object_name') }}">
                    @error('object_name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="balance_keeper">Балансда сақловчи</label>
                    <input class="form-control" type="text" name="balance_keeper" id="balance_keeper"
                        value="{{ old('balance_keeper') }}" placeholder="Хокимият">
                    @error('balance_keeper')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                @include('inc.__address')

                <div class="mb-3">
                    <label for="location">Мўлжал</label>
                    <input class="form-control" type="text" name="location" id="location"
                        value="{{ old('location') }}">
                    @error('location')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="land_area">Ер майдони (кв.м)</label>
                    <input class="form-control" type="number" name="land_area" id="land_area"
                        value="{{ old('land_area') }}">
                    @error('land_area')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="building_area">Бино майдони (кв.м)</label>
                    <input class="form-control" type="number" name="building_area" id="building_area"
                        value="{{ old('building_area') }}">
                    @error('building_area')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <label for="gas">Газ</label>
                <select class="form-control form-select mb-3" name="gas" id="gas">
                    <option value="Мавжуд" {{ old('gas') == 'Мавжуд' ? 'selected' : '' }}>Мавжуд</option>
                    <option value="Мавжуд эмас" {{ old('gas') == 'Мавжуд эмас' ? 'selected' : '' }}>Мавжуд эмас</option>
                </select>
                @error('gas')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
                <label for="water">Сув</label>
                <select class="form-control form-select mb-3" name="water" id="water">
                    <option value="Мавжуд" {{ old('water') == 'Мавжуд' ? 'selected' : '' }}>Мавжуд</option>
                    <option value="Мавжуд эмас" {{ old('water') == 'Мавжуд эмас' ? 'selected' : '' }}>Мавжуд эмас</option>
                </select>
                @error('water')
                    <div class="text-danger">{{ $message }}</div>
                @enderror

                <label for="electricity">Электр</label>
                <select class="form-control form-select mb-3" name="electricity" id="electricity">
                    <option value="Мавжуд" {{ old('electricity') == 'Мавжуд' ? 'selected' : '' }}>Мавжуд</option>
                    <option value="Мавжуд эмас" {{ old('electricity') == 'Мавжуд эмас' ? 'selected' : '' }}>Мавжуд эмас
                    </option>
                </select>
                @error('electricity')
                    <div class="text-danger">{{ $message }}</div>
                @enderror

                <label for="building_type">Бино тури</label>
                <select name="building_type" id="building_type" class="form-control" required>
                    <option value="" disabled selected>Выберите тип недвижимости</option>
                    <option value="kopQavatliUy" {{ old('building_type') == 'kopQavatliUy' ? 'selected' : '' }}>
                        Кўп қаватли уйдаги нотурар жой
                    </option>
                    <option value="AlohidaSavdoDokoni"
                        {{ old('building_type') == 'AlohidaSavdoDokoni' ? 'selected' : '' }}>Алоҳида нотурар жой
                    </option>
                </select>

                @error('building_type')
                    <div class="text-danger">{{ $message }}</div>
                @enderror

                <div class="mb-3">
                    <label for="additional_info">Қўшимча маълумот</label>
                    <input class="form-control" type="text" name="additional_info" id="additional_info"
                        value="{{ old('additional_info') }}">
                    @error('additional_info')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="kadastr_raqami">Кадастр рақами</label>
                    <input class="form-control" type="text" name="kadastr_raqami" id="kadastr_raqami"
                        value="{{ old('kadastr_raqami') }}" {{-- pattern="\d{2}:\d{2}:\d{2}:\d{2}:\d{2}:\d{4}:\d{4}:\d{3}" --}}
                        title="Format: 11:04:42:01:03:0136:0001:045" placeholder="10:06:03:01:02:5038:0001:045">

                    <small id="kadastrHelp" class="form-text text-muted">
                        Please enter the cadastral number in the format: 10:06:03:01:02:5038:0001:045
                    </small>
                </div>

                {{-- <div class="form-group">
                    <label for="kadastr_pdf">Кадастр файл</label>
                    <input type="file" id="kadastr_pdf" name="kadastr_pdf" class="form-control">
                </div> --}}

                <div class="form-group">
                    <label for="ijara_shartnoma_nusxasi_pdf">Ижара шартнома нусхаси</label>
                    <input type="file" id="ijara_shartnoma_nusxasi_pdf" name="ijara_shartnoma_nusxasi_pdf"
                        class="form-control">
                </div>

                <div class="form-group mb-4">
                    <label for="qoshimcha_fayllar_pdf">Қошимча файллар</label>
                    <input type="file" id="qoshimcha_fayllar_pdf" name="qoshimcha_fayllar_pdf" class="form-control">
                </div>

                {{-- ---------------------------------- --}}
                {{-- ------------------------------------------- --}}

                <label for="object_type">Фаолияти тури:</label>
                <select name="object_type" class="form-control">
                    <option value="Иишлаб чиқариш"
                        {{ old('object_type', $aktiv->object_type ?? '') == 'Иишлаб чиқариш' ? 'selected' : '' }}>
                        Иишлаб чиқариш
                    </option>
                    <option value="савдо"
                        {{ old('object_type', $aktiv->object_type ?? '') == 'савдо' ? 'selected' : '' }}>
                        савдо
                    </option>
                    <option value="хизмат"
                        {{ old('object_type', $aktiv->object_type ?? '') == 'хизмат' ? 'selected' : '' }}>
                        хизмат
                    </option>
                    <option value="қурилиш"
                        {{ old('object_type', $aktiv->object_type ?? '') == 'қурилиш' ? 'selected' : '' }}>
                        қурилиш
                    </option>
                    <option value="таълим"
                        {{ old('object_type', $aktiv->object_type ?? '') == 'таълим' ? 'selected' : '' }}>
                        таълим
                    </option>
                    <option value="спорт"
                        {{ old('object_type', $aktiv->object_type ?? '') == 'спорт' ? 'selected' : '' }}>
                        спорт
                    </option>
                    <option value="наширёт"
                        {{ old('object_type', $aktiv->object_type ?? '') == 'наширёт' ? 'selected' : '' }}>
                        наширёт
                    </option>
                </select>



                {{-- <label for="document_type">Ҳужжат тури:</label>
                <select name="document_type" class="form-control">
                    <option value="ҳоким қарори"
                        {{ old('document_type', $aktiv->document_type ?? '') == 'ҳоким қарори' ? 'selected' : '' }}>Ҳоким
                        қарори</option>
                    <option value="ордер"
                        {{ old('document_type', $aktiv->document_type ?? '') == 'ордер' ? 'selected' : '' }}>Ордер</option>
                    <option value="ижара шартнома"
                        {{ old('document_type', $aktiv->document_type ?? '') == 'ижара шартнома' ? 'selected' : '' }}>Ижара
                        шартнома</option>
                </select> --}}

                <label for="reason_not_active">Фаолият юритмаётганлиги сабаби:</label>
                <input type="text" name="reason_not_active" class="form-control"
                    value="{{ old('reason_not_active', $aktiv->reason_not_active ?? '') }}">

                <label for="ready_for_rent">Ижарага беришга тайёрлиги:</label>
                <select name="ready_for_rent" class="form-control">
                    <option value="ха"
                        {{ old('ready_for_rent', $aktiv->ready_for_rent ?? '') == 'ха' ? 'selected' : '' }}>Ҳа</option>
                    <option value="йўқ"
                        {{ old('ready_for_rent', $aktiv->ready_for_rent ?? '') == 'йўқ' ? 'selected' : '' }}>Йўқ</option>
                </select>

                <label for="rental_agreement_status">Ижара шартномаси ҳолати:</label>
                <select name="rental_agreement_status" class="form-control">
                    <option value="бор"
                        {{ old('rental_agreement_status', $aktiv->rental_agreement_status ?? '') == 'бор' ? 'selected' : '' }}>
                        бор</option>

                    <option value="йўқ"
                        {{ old('rental_agreement_status', $aktiv->rental_agreement_status ?? '') == 'йўқ' ? 'selected' : '' }}>
                        йўқ</option>
                </select>

                <label for="unused_duration">Фойдаланилмаган муддат:</label>
                <select name="unused_duration" class="form-control">

                    <option value="1 ой бўлди"
                        {{ old('unused_duration', $aktiv->unused_duration ?? '') == '1 ой бўлди' ? 'selected' : '' }}>1 ой
                        бўлди</option>
                    <option value="3 ой бўлди"
                        {{ old('unused_duration', $aktiv->unused_duration ?? '') == '3 ой бўлди' ? 'selected' : '' }}>3 ой
                        бўлди</option>

                    <option value="6 ой бўлди"
                        {{ old('unused_duration', $aktiv->unused_duration ?? '') == '6 ой бўлди' ? 'selected' : '' }}>6 ой
                        бўлди</option>

                    <option value="1 йил бўлди"
                        {{ old('unused_duration', $aktiv->unused_duration ?? '') == '1 йил бўлди' ? 'selected' : '' }}>1
                        йил бўлди</option>

                    <option value="1 йил Ундан кўп"
                        {{ old('unused_duration', $aktiv->unused_duration ?? '') == '1 йил Ундан кўп' ? 'selected' : '' }}>
                        1 йил Ундан кўп</option>
                </select>


                <label for="provided_assistance">Берилган амалий ёрдам:</label>
                <select name="provided_assistance" class="form-control">
                    <option value="кредит берилди"
                        {{ old('provided_assistance', $aktiv->provided_assistance ?? '') == 'кредит берилди' ? 'selected' : '' }}>
                        кредит берилди</option>
                    <option value="маслахат берилди"
                        {{ old('provided_assistance', $aktiv->provided_assistance ?? '') == 'маслахат берилди' ? 'selected' : '' }}>
                        маслахат берилди</option>

                    <option value="ижарачи топиб берилди"
                        {{ old('provided_assistance', $aktiv->provided_assistance ?? '') == 'ижарачи топиб берилди' ? 'selected' : '' }}>
                        ижарачи топиб берилди</option>
                </select>

                {{-- <label for="start_date">Фаолият юритишни бошлаган сана:</label>
                <input type="date" name="start_date" class="form-control"
                    value="{{ old('start_date', $aktiv->start_date ?? '') }}"> --}}

                <label for="additional_notes">Изоҳ:</label>
                <textarea name="additional_notes" rows="4" class="form-control">{{ old('additional_notes', $aktiv->additional_notes ?? '') }}</textarea>




                <label for="stir">СТИР:</label>
                <input type="text" name="stir" class="form-control"
                    value="{{ old('stir', $aktiv->stir ?? '') }}">

                <!-- Ижарачи тел рақами -->
                <label for="tenant_phone_number">Ижарачи тел рақами</label>
                <input type="text" name="tenant_phone_number" id="tenant_phone_number" class="form-control"
                    placeholder="+998 90 123 45 67" value="{{ old('tenant_phone_number') }}">

                <div class="col-lg-12 col-md-12 col-12 mb-3">
                    <label for="ijaraga_berishga_tayyorligi">Ижарага беришга тайёрлиги</label>
                    <select name="ijaraga_berishga_tayyorligi" id="ijaraga_berishga_tayyorligi" class="form-control">
                        <option value="">Танланг</option>
                        <option value="yeap" {{ old('ijaraga_berishga_tayyorligi') == 'yeap' ? 'selected' : '' }}>Ха
                            Ижарага бермоқчи
                        </option>
                        <option value="not" {{ old('ijaraga_berishga_tayyorligi') == 'not' ? 'selected' : '' }}>Йўқ ози
                            бошқармоқчи
                        </option>
                    </select>
                </div>

                <!-- Ижарага бериш суммаси -->
                <div class="col-lg-12 col-md-12 col-12 mb-3" id="ijara_summa_wanted_container" style="display: none;">
                    <label for="ijara_summa_wanted">Ижарага режалаштирган сумма <span
                            style="color: red !important;">(фақат сўмда ёзилади)</label>
                    <input type="number" step="0.01" min="9999" name="ijara_summa_wanted"
                        id="ijara_summa_wanted" class="form-control" placeholder="Суммани киритинг 1 000 000 сўм"
                        value="{{ old('ijara_summa_wanted') }}">
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const ijaragaTayyorligiSelect = document.getElementById('ijaraga_berishga_tayyorligi');
                        const ijaraSummaWantedContainer = document.getElementById('ijara_summa_wanted_container');

                        function toggleIjaraSummaWanted() {
                            ijaraSummaWantedContainer.style.display = ijaragaTayyorligiSelect.value === 'yeap' ? 'block' :
                                'none';
                        }

                        ijaragaTayyorligiSelect.addEventListener('change', toggleIjaraSummaWanted);

                        // Initial toggle based on the saved value
                        toggleIjaraSummaWanted();
                    });
                </script>
                <!-- Ижарага бериш суммаси -->
                <label for="ijara_summa_fakt">Ижарага суммаси факт... <span style="color: red"><span
                            style="color: red !important;">(фақат сўмда ёзилади)</span></span></label>
                <input type="number" step="0.01" min="9999" name="ijara_summa_fakt" id="ijara_summa_fakt"
                    class="form-control" placeholder="Суммани киритинг 1 000 000 сўм"
                    value="{{ old('ijara_summa_fakt') }}">


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

                    // Kadastr formatting script
                    // document.getElementById('kadastr_raqami').addEventListener('input', function(e) {
                    //     let value = e.target.value.replace(/[^0-9]/g, '');
                    //     let formattedValue = '';

                    //     if (value.length > 0) formattedValue += value.substring(0, 2);
                    //     if (value.length > 2) formattedValue += ':' + value.substring(2, 4);
                    //     if (value.length > 4) formattedValue += ':' + value.substring(4, 6);
                    //     if (value.length > 6) formattedValue += ':' + value.substring(6, 8);
                    //     if (value.length > 8) formattedValue += ':' + value.substring(8, 10);
                    //     if (value.length > 10) formattedValue += ':' + value.substring(10, 14);

                    //     e.target.value = formattedValue;
                    // });
                </script>



            </div>
            <!-- Right Column -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="text-danger">Файлларни юклаш (Камида 4 та расм мажбурий)</label>
                </div>

                <div id="fileInputsContainer">
                    @for ($i = 1; $i <= 4; $i++)
                        <div class="mb-3" id="fileInput{{ $i }}">
                            <label for="file{{ $i }}">Файл {{ $i }}</label>
                            <div class="input-group">
                                <input type="file" class="form-control" name="files[]" id="file{{ $i }}"
                                    accept="image/*" required>
                                <button type="button" class="btn btn-secondary"
                                    onclick="openCameraModal('file{{ $i }}')">📷</button>
                            </div>
                        </div>
                    @endfor
                </div>

                <div id="file-error" class="text-danger mb-3"></div>
                <div id="file-upload-container"></div>
                <button type="button" class="btn btn-secondary mb-3" id="add-file-btn">Янги файл қўшиш</button>

                <div class="mb-3">
                    <button id="find-my-location" type="button" class="btn btn-primary mb-3">Менинг жойлашувимни
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
                    <input class="form-control" type="text" name="geolokatsiya" id="geolokatsiya" readonly required
                        value="{{ old('geolokatsiya') }}">
                    @error('geolokatsiya')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success" id="submit-btn">Сақлаш</button>
    </form>
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
