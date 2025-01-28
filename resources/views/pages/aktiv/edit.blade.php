@extends('layouts.admin')

@section('content')
    <h1>Активни таҳрирлаш</h1>

    <form method="POST" action="{{ route('aktivs.update', $aktiv->id) }}" enctype="multipart/form-data" id="aktiv-form">
        @csrf
        @method('PUT')

        <input type="hidden" name="user_id" value="{{ $aktiv->user_id }}">
        <div class="row my-3">
            <!-- Left Column -->
            <div class="col-md-6">
                <!-- Form Inputs -->
                <div class="mb-3">
                    <label for="object_name">Объект номи</label>
                    <input class="form-control" type="text" name="object_name" id="object_name"
                        value="{{ old('object_name', $aktiv->object_name) }}">
                    @error('object_name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="balance_keeper">Балансда сақловчи</label>
                    <input class="form-control" type="text" name="balance_keeper" id="balance_keeper"
                        value="{{ old('balance_keeper', $aktiv->balance_keeper) }}">
                    @error('balance_keeper')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>


                <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0/css/select2.min.css" rel="stylesheet" />
                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0/js/select2.min.js"></script>

                <div class="card mb-3">
                    <div class="card-header">
                        <h5>Манзил маълумотлари</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Худуд номи:</strong>
                            {{ $aktiv->subStreet->district->region->name_uz ?? 'Маълумот йўқ' }}
                        </div>
                        <div class="mb-3">
                            <strong>Туман номи:</strong>
                            {{ $aktiv->subStreet->district->name_uz ?? 'Маълумот йўқ' }}
                        </div>
                        <div class="mb-3">
                            <strong>Мфй номи:</strong>
                            {{ $aktiv->street->name ?? 'Маълумот йўқ' }}
                        </div>
                        <div class="mb-3">
                            <strong>Кўча номи:</strong>
                            {{ $aktiv->subStreet->name ?? 'Маълумот йўқ' }}
                        </div>
                    </div>
                </div>

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
                                        {{ $region->id == old('region_id', optional($aktiv->subStreet->district->region)->id) ? 'selected' : '' }}>
                                        {{ $region->name_uz }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-6 col-md-12 col-12 mb-3">
                            <label for="district_id">Туман</label>
                            <select class="form-control district_id select2" name="district_id" id="district_id" required>
                                <option value="" disabled selected>Туманни танланг</option>
                                @foreach ($districts as $district)
                                    <option value="{{ $district->id }}"
                                        {{ $district->id == old('district_id', optional($aktiv->subStreet->district)->id) ? 'selected' : '' }}>
                                        {{ $district->name_uz }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-6 col-md-12 col-12 mb-3">
                            <label for="street_id" class="me-2">Мфй<span
                                    style="color: red;font-weight: bold;">MAJBURIY</span></label>
                            <div class="d-flex align-items-end">
                                <select class="form-control street_id select2" name="street_id" id="street_id" required>
                                    <option value="" disabled selected>Мфй ни танланг</option>
                                    @foreach ($streets as $street)
                                        <option value="{{ $street->id }}"
                                            {{ $street->id == old('street_id', $aktiv->street_id) ? 'selected' : '' }}>
                                            {{ $street->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-primary ms-2" id="add_street_btn"
                                    title="Мфй қошиш">+</button>
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-12 col-12 mb-3">
                            <label for="substreet_id" class="me-2">Кўча<span
                                    style="color: red;font-weight: bold;">MAJBURIЙ</span></label>
                            <div class="d-flex align-items-end">
                                <select class="form-control sub_street_id select2" name="sub_street_id" id="substreet_id"
                                    required>
                                    <option value="" disabled selected>Кўчани танланг</option>
                                    @foreach ($substreets as $substreet)
                                        <option value="{{ $substreet->id }}"
                                            {{ $substreet->id == old('sub_street_id', $aktiv->sub_street_id) ? 'selected' : '' }}>
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
                                        value="{{ old('home_number', $aktiv->home_number) }}" />
                                </div>
                                <span class="text-danger error-message" id="home_number_error"></span>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="apartment_number" class="me-2">Квартира рақами (Мажбурий эмас)</label>
                                <div class="d-flex align-items-end">
                                    <input class="form-control" name="apartment_number" type="text"
                                        value="{{ old('apartment_number', $aktiv->apartment_number) }}"
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
                        // var selectedRegionId = "{{ old('region_id', optional($aktiv->subStreet->district->region)->id) }}";
                        // var selectedDistrictId = "{{ old('district_id', optional($aktiv->subStreet->district)->id) }}";
                        // var selectedStreetId = "{{ old('street_id', $aktiv->street_id) }}";
                        // var selectedSubStreetId = "{{ old('sub_street_id', $aktiv->sub_street_id) }}";

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


                <div class="test">
                    <div class="row">

                        <div class="col-lg-6 col-md-12 col-12 mb-3">
                            <label for="location">Мўлжал</label>
                            <input class="form-control" type="text" name="location" id="location"
                                value="{{ old('location', $aktiv->location) }}">
                            @error('location')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-lg-6 col-md-12 col-12 mb-3">
                            <label for="land_area">Ер майдони (кв.м)</label>
                            <input class="form-control" type="number" name="land_area" id="land_area"
                                value="{{ old('land_area', $aktiv->land_area) }}">
                            @error('land_area')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-lg-6 col-md-12 col-12 mb-3">
                            <label for="building_area">Бино майдони (кв.м)</label>
                            <input class="form-control" type="number" name="building_area" id="building_area"
                                value="{{ old('building_area', $aktiv->building_area) }}">
                            @error('building_area')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="col-lg-6 col-md-12 col-12 mb-3">

                            <label for="gas">Газ</label>
                            <select class="form-control form-select mb-3" name="gas" id="gas">
                                <option value="Мавжуд" {{ old('gas', $aktiv->gas) == 'Мавжуд' ? 'selected' : '' }}>Мавжуд
                                </option>
                                <option value="Мавжуд эмас"
                                    {{ old('gas', $aktiv->gas) == 'Мавжуд эмас' ? 'selected' : '' }}>
                                    Мавжуд
                                    эмас</option>
                            </select>
                            @error('gas')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-lg-6 col-md-12 col-12 mb-3">

                            <label for="water">Сув</label>
                            <select class="form-control form-select mb-3" name="water" id="water">
                                <option value="Мавжуд" {{ old('water', $aktiv->water) == 'Мавжуд' ? 'selected' : '' }}>
                                    Мавжуд
                                </option>
                                <option value="Мавжуд эмас"
                                    {{ old('water', $aktiv->water) == 'Мавжуд эмас' ? 'selected' : '' }}>
                                    Мавжуд
                                    эмас</option>
                            </select>
                            @error('water')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-lg-6 col-md-12 col-12 mb-3">

                            <label for="electricity">Электр</label>
                            <select class="form-control form-select mb-3" name="electricity" id="electricity">
                                <option value="Мавжуд"
                                    {{ old('electricity', $aktiv->electricity) == 'Мавжуд' ? 'selected' : '' }}>
                                    Мавжуд</option>
                                <option value="Мавжуд эмас"
                                    {{ old('electricity', $aktiv->electricity) == 'Мавжуд эмас' ? 'selected' : '' }}>Мавжуд
                                    эмас
                                </option>
                            </select>
                            @error('electricity')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror

                        </div>

                        <div class="col-lg-6 col-md-12 col-12 mb-3">

                            <label for="building_type">Бино тури</label>
                            <select name="building_type" id="building_type" class="form-control" required>
                                <option value="" disabled
                                    {{ old('building_type', $aktiv->building_type) == '' ? 'selected' : '' }}>Выберите тип
                                    недвижимости
                                </option>

                                <option value="kopQavatliUy"
                                    {{ old('building_type', $aktiv->building_type) == 'kopQavatliUy' ? 'selected' : '' }}>
                                    kopQavatliUy
                                </option>
                                <option value="AlohidaSavdoDokoni"
                                    {{ old('building_type', $aktiv->building_type) == 'AlohidaSavdoDokoni' ? 'selected' : '' }}>
                                    AlohidaSavdoDokoni
                                </option>
                            </select>

                            @error('building_type')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>



                        <div class="col-lg-6 col-md-12 col-12 mb-3">
                            <label for="additional_info">Қўшимча маълумот</label>
                            <input class="form-control" type="text" name="additional_info" id="additional_info"
                                value="{{ old('additional_info', $aktiv->additional_info) }}">
                            @error('additional_info')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-lg-6 col-md-12 col-12 mb-3">
                            <label for="kadastr_raqami">Кадастр рақами</label>
                            <input class="form-control" type="text" name="kadastr_raqami" id="kadastr_raqami"
                                value="{{ old('kadastr_raqami', $aktiv->kadastr_raqami) }}"
                                title="Format: 11:04:42:01:03:0136" placeholder="11:04:42:01:03:0136">
                            <small id="kadastrHelp" class="form-text text-muted">
                                Please enter the cadastral number in the format: 11:04:42:01:03:0136
                            </small>
                        </div>

                        <div class="col-lg-6 col-md-12 col-12 mb-3">

                            <div class="form-group">
                                <label for="kadastr_pdf">Кадастр файл</label>
                                <input type="file" id="kadastr_pdf" name="kadastr_pdf" class="form-control">
                            </div>
                        </div>

                        <div class="col-lg-12 col-md-12 col-12 mb-3">

                            <div class="form-group">
                                <label for="hokim_qarori_pdf">Балансга қабул қилиш учун асос болган хужжат</label>
                                <input type="file" id="hokim_qarori_pdf" name="hokim_qarori_pdf"
                                    class="form-control">
                            </div>
                        </div>

                        <div class="col-lg-12 col-md-12 col-12 mb-3">

                            <div class="form-group mb-4">
                                <label for="transfer_basis_pdf">3-шахсга йоки бошқа шахсга бериш учун асос болган
                                    хужжат</label>
                                <input type="file" id="transfer_basis_pdf" name="transfer_basis_pdf"
                                    class="form-control">
                            </div>

                        </div>
                        {{-- ------------------------------------------- --}}

                        <div class="col-lg-6 col-md-12 col-12 mb-3">

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
                        </div>


                        <div class="col-lg-6 col-md-12 col-12 mb-3">

                            <label for="document_type">Ҳужжат тури:</label>
                            <select name="document_type" class="form-control">
                                <option value="ҳоким қарори"
                                    {{ old('document_type', $aktiv->document_type ?? '') == 'ҳоким қарори' ? 'selected' : '' }}>
                                    Ҳоким
                                    қарори</option>
                                <option value="ордер"
                                    {{ old('document_type', $aktiv->document_type ?? '') == 'ордер' ? 'selected' : '' }}>
                                    Ордер
                                </option>
                                <option value="ижара шартнома"
                                    {{ old('document_type', $aktiv->document_type ?? '') == 'ижара шартнома' ? 'selected' : '' }}>
                                    Ижара
                                    шартнома</option>
                            </select>
                        </div>


                        <div class="col-lg-6 col-md-12 col-12 mb-3">

                            <label for="reason_not_active">Фаолият юритмаётганлиги сабаби:</label>
                            <input type="text" name="reason_not_active" class="form-control"
                                value="{{ old('reason_not_active', $aktiv->reason_not_active ?? '') }}">


                        </div>

                        <div class="col-lg-6 col-md-12 col-12 mb-3">

                            <label for="ready_for_rent">Ижарага беришга тайёрлиги:</label>
                            <select name="ready_for_rent" class="form-control">
                                <option value="ха"
                                    {{ old('ready_for_rent', $aktiv->ready_for_rent ?? '') == 'ха' ? 'selected' : '' }}>Ҳа
                                </option>
                                <option value="йўқ"
                                    {{ old('ready_for_rent', $aktiv->ready_for_rent ?? '') == 'йўқ' ? 'selected' : '' }}>
                                    Йўқ
                                </option>
                            </select>
                        </div>

                        <div class="col-lg-6 col-md-12 col-12 mb-3">

                            <label for="rental_agreement_status">Ижара шартномаси ҳолати:</label>
                            <select name="rental_agreement_status" class="form-control">
                                <option value="энди тузилади"
                                    {{ old('rental_agreement_status', $aktiv->rental_agreement_status ?? '') == 'энди тузилади' ? 'selected' : '' }}>
                                    энди тузилади</option>
                                <option value="хозир топполмаяпман"
                                    {{ old('rental_agreement_status', $aktiv->rental_agreement_status ?? '') == 'хозир топполмаяпман' ? 'selected' : '' }}>
                                    хозир топполмаяпман</option>

                                <option value="бор"
                                    {{ old('rental_agreement_status', $aktiv->rental_agreement_status ?? '') == 'бор' ? 'selected' : '' }}>
                                    бор</option>

                                <option value="йўқ"
                                    {{ old('rental_agreement_status', $aktiv->rental_agreement_status ?? '') == 'йўқ' ? 'selected' : '' }}>
                                    йўқ</option>
                            </select>
                        </div>


                        <div class="col-lg-6 col-md-12 col-12 mb-3">

                            <label for="unused_duration">Фойдаланилмаган муддат:</label>
                            <select name="unused_duration" class="form-control">
                                <option value="1 ой бўлди"
                                    {{ old('unused_duration', $aktiv->unused_duration ?? '') == '1 ой бўлди' ? 'selected' : '' }}>
                                    1
                                    ой
                                    бўлди</option>
                                <option value="3 ой бўлди"
                                    {{ old('unused_duration', $aktiv->unused_duration ?? '') == '3 ой бўлди' ? 'selected' : '' }}>
                                    3
                                    ой
                                    бўлди</option>

                                <option value="6 ой бўлди"
                                    {{ old('unused_duration', $aktiv->unused_duration ?? '') == '6 ой бўлди' ? 'selected' : '' }}>
                                    6
                                    ой
                                    бўлди</option>

                                <option value="1 йил бўлди"
                                    {{ old('unused_duration', $aktiv->unused_duration ?? '') == '1 йил бўлди' ? 'selected' : '' }}>
                                    1
                                    йил бўлди</option>

                                <option value="1 йил Ундан кўп"
                                    {{ old('unused_duration', $aktiv->unused_duration ?? '') == '1 йил Ундан кўп' ? 'selected' : '' }}>
                                    1 йил Ундан кўп</option>
                            </select>
                        </div>


                        <div class="col-lg-6 col-md-12 col-12 mb-3">

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
                        </div>


                        <div class="col-lg-6 col-md-12 col-12 mb-3">

                            <label for="start_date">Фаолият юритишни бошлаган сана:</label>
                            <input type="date" name="start_date" class="form-control"
                                value="{{ old('start_date', $aktiv->start_date ?? '') }}">
                        </div>

                        <div class="col-lg-6 col-md-12 col-12 mb-3">

                            <label for="additional_notes">Изоҳ:</label>
                            <textarea name="additional_notes" class="form-control">{{ old('additional_notes', $aktiv->additional_notes ?? '') }}</textarea>
                        </div>

                        <div class="col-lg-6 col-md-12 col-12 mb-3">

                            <label for="working_24_7">24/7 режимда ишлайдими?</label>
                            <select name="working_24_7" class="form-control">
                                <option value="1"
                                    {{ old('working_24_7', $aktiv->working_24_7 ?? '') == '1' ? 'selected' : '' }}>
                                    Ҳа</option>
                                <option value="0"
                                    {{ old('working_24_7', $aktiv->working_24_7 ?? '') == '0' ? 'selected' : '' }}>
                                    Йўқ</option>
                            </select>
                        </div>


                        <div class="col-lg-6 col-md-12 col-12 mb-3">

                            <label for="owner">Мулкдор:</label>
                            <input type="text" name="owner" class="form-control"
                                value="{{ old('owner', $aktiv->owner ?? '') }}">
                        </div>


                        <div class="col-lg-6 col-md-12 col-12 mb-3">

                            <label for="STIR">СТИР:</label>
                            <input type="text" name="STIR" class="form-control"
                                value="{{ old('STIR', $aktiv->STIR ?? '') }}">
                        </div>
                    </div>

                </div>
            </div>
            <!-- Right Column -->
            <div class="col-md-6">
                <!-- Existing Files -->
                <div class="mb-3">
                    <label class="text-primary">Мавжуд файллар</label>
                    <div id="existing-files" class="mb-3">
                        @foreach ($aktiv->files as $file)
                            <div class="existing-file mb-2">
                                <a href="{{ asset('storage/' . $file->path) }}" target="_blank">Файлни кўриш</a>
                                <label>
                                    <input type="checkbox" name="delete_files[]" value="{{ $file->id }}">
                                    Ўчириш
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- File upload fields -->
                <div class="mb-3">
                    <label class="text-danger">Янги файлларни юклаш (Камида 4 та файл бўлиши шарт)</label>
                </div>
                <!-- Error message display -->
                <div id="file-error" class="text-danger mb-3"></div>

                <!-- Container to hold new file inputs -->
                <div id="file-upload-container" class="row">
                    <div class="col-lg-6 col-md-12 col-12 mb-3">
                        <label for="file1">Биринчи файл</label>
                        <input type="file" class="form-control" name="files[]" id="file1">
                    </div>
                    <div class="col-lg-6 col-md-12 col-12 mb-3">
                        <label for="file2">Иккинчи файл</label>
                        <input type="file" class="form-control" name="files[]" id="file2">
                    </div>
                    <div class="col-lg-6 col-md-12 col-12 mb-3">
                        <label for="file3">Учинчи файл</label>
                        <input type="file" class="form-control" name="files[]" id="file3">
                    </div>
                    <div class="col-lg-6 col-md-12 col-12 mb-3">
                        <label for="file4">Тўртинчи файл</label>
                        <input type="file" class="form-control" name="files[]" id="file4">
                    </div>
                </div>

                {{-- <button type="button" class="btn btn-secondary mb-3" onclick="addFileInput()">Янги файл қўшиш</button> --}}

                <!-- Map Section -->
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

                <!-- Hidden Fields for Coordinates -->
                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $aktiv->latitude) }}">
                <input type="hidden" name="longitude" id="longitude"
                    value="{{ old('longitude', $aktiv->longitude) }}">

                <!-- Geolocation URL Field -->
                <div class="mb-3">
                    <label for="geolokatsiya">Геолокация (координата)</label>
                    <input class="form-control" type="text" name="geolokatsiya" id="geolokatsiya" readonly required
                        value="{{ old('geolokatsiya', $aktiv->geolokatsiya) }}">
                    @error('geolokatsiya')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-success" id="submit-btn">Сақлаш</button>
    </form>
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
