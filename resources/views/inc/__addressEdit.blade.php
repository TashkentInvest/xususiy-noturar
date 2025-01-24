
    <div class="card mb-3">
        <div class="card-header">
            <h5>Манзил маълумотлари (Address Information)</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <strong>Вилоят номи (Region Name):</strong>
                {{ $aktiv->subStreet->district->region->name_uz ?? 'Маълумот йўқ' }}
            </div>
            <div class="mb-3">
                <strong>Туман номи (District Name):</strong>
                {{ $aktiv->subStreet->district->name_uz ?? 'Маълумот йўқ' }}
            </div>
            <div class="mb-3">
                <strong>Мфй номи (MFY Name):</strong>
                {{ $aktiv->street->name ?? 'Маълумот йўқ' }}
            </div>
            <div class="mb-3">
                <strong>Кўча номи (Sub Street Name):</strong>
                {{ $aktiv->subStreet->name ?? 'Маълумот йўқ' }}
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h5>Манзилни озгартириш (Edit Address)</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label for="region_id">Худуд</label>
                <select class="form-control region_id select2" name="region_id" id="region_id">
                    <option value="">Худудни танланг</option>
                    @foreach ($regions as $region)
                        <option value="{{ $region->id }}"
                            {{ $region->id == old('region_id', optional($aktiv->subStreet->district->region)->id) ? 'selected' : '' }}>
                            {{ $region->name_uz }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="district_id">Район</label>
                <select class="form-control district_id select2" name="district_id" id="district_id">
                    <option value="">Туманни танланг</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="street_id" class="me-2">Мфй <span style="color: red;font-weight: bold;">
                        MAJBURIY</span></label>
                <div class="d-flex align-items-end">
                    <select class="form-control street_id select2" name="street_id" id="street_id" required>
                        <option value="">Мфй ни танланг</option>
                    </select>
                    <button type="button" class="btn btn-primary ms-2" id="add_street_btn" title="Мфй қошиш">+</button>
                </div>
            </div>

            <div class="mb-3">
                <label for="substreet_id" class="me-2">Кўча <span style="color: red;font-weight: bold;">
                        MAJBURIY</span></label>
                <div class="d-flex align-items-end">
                    <select class="form-control sub_street_id select2" name="sub_street_id" id="substreet_id" required>
                        <option value="">Кўчани танланг</option>
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
                            value="{{ old('apartment_number', $aktiv->apartment_number) }}" id="apartment_number" />
                    </div>
                    <span class="text-danger error-message" id="apartment_number_error"></span>
                </div>
            </div>
        </div>
    </div>

    