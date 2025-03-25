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
                        <input type="text" name="stir" id="stirField" class="form-control form-control-lg shadow-sm"
                            placeholder="📊 СТИР рақами" style="display: none;">
                    </div>

                    <!-- Фойдаланиш мумкинми? -->
                    <div class="mt-4">
                        <label class="form-label fw-bold">❓ Фойдаланиш мумкинми?</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input custom-radio" type="radio" name="does_can_we_use_yer_tola"
                                    value="1" onclick="showUseFields(true)">
                                <label class="form-check-label fw-bold">✅ Ҳа</label>
                            </div>
                            <div class="form-check ml-3">
                                <input class="form-check-input custom-radio" type="radio" name="does_can_we_use_yer_tola"
                                    value="0" onclick="showUseFields(false)">
                                <label class="form-check-label fw-bold">❌ Йўқ</label>
                            </div>
                        </div>
                    </div>

                    <!-- Агар фойдаланиш мумкин бўлса -->
                    <div id="useFields" class="mt-4 p-3 border rounded bg-light shadow-sm" style="display: none;">
                        <input type="number" name="ijaraga_berilgan_qismi_yer_tola"
                            class="form-control form-control-lg shadow-sm mb-2" placeholder="📏 Ижарага берилган қисм (м²)">
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
