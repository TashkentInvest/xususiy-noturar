@extends('layouts.admin')

@section('content')
    <div class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="text-primary fw-bold">🏠 Ер Тўлалар Рўйхати</h2>
            <a href="{{ route('yertola.create') }}" class="btn btn-success">
                ➕ Янги қўшиш
            </a>
        </div>

        <div class="card shadow-lg rounded-4 border-0 p-4">
            @if ($yertolas->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover text-center align-middle">
                        <thead class="table-primary">
                            <tr>
                                <th>#</th>
                                <th>Global id</th>
                                <th class="text-start" style="min-width: 250px;">📍 Манзил</th>
                                <th>🏠 Ер тўла</th>
                                <th>✅ Фойдаланиш</th>
                                <th>⚙ Амаллар</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($yertolas as $index => $yertola)
                                <tr>
                                    <td>{{ $yertolas->total() - (($yertolas->currentPage() - 1) * $yertolas->perPage() + $index) }}</td>
                                    <td>{{$yertola->id}}</td>
                                    <td class="text-start">
                                        {{ $yertola->subStreet->district->name_uz ?? 'Маълумот йўқ' }} т.,
                                        {{ $yertola->street->name ?? 'Маълумот йўқ' }} МФЙ,
                                        {{ $yertola->subStreet->name ?? 'Маълумот йўқ' }},
                                        {{ $yertola->home_number ?? 'Маълумот йўқ' }}
                                    </td>
                                    <td>
                                        <span
                                            class="badge text-light {{ $yertola->does_exists_yer_tola ? 'bg-success' : 'bg-danger' }}">
                                            {{ $yertola->does_exists_yer_tola ? 'Мавжуд' : 'Мавжуд эмас' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge text-light {{ $yertola->does_can_we_use_yer_tola ? 'bg-primary' : 'bg-warning' }}">
                                            {{ $yertola->does_can_we_use_yer_tola ? 'Мумкин' : 'Мумкин эмас' }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#detailsModal{{ $yertola->id }}">
                                            🔍 Маълумот
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center text-muted py-4">
                    <i class="fas fa-folder-open fa-3x mb-3"></i>
                    <p>🚫 Ҳеч қандай ер тўла топилмади.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Modals -->
    @foreach ($yertolas as $yertola)
        <div class="modal fade" id="detailsModal{{ $yertola->id }}" tabindex="-1"
            aria-labelledby="modalLabel{{ $yertola->id }}" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-light border-bottom border-3 border-primary">
                        <h5 class="modal-title" id="modalLabel{{ $yertola->id }}">
                            <i class="fas fa-folder-open text-primary me-2"></i>
                            <span class="fw-bold">Ер Тўла Маълумотлари</span>
                            <small class="text-muted ms-2">#{{ $yertola->id }}</small>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-0">
                        <!-- Quick Stats Dashboard at top -->
                        <div class="bg-light p-3 border-bottom">
                            <div class="row g-3 text-center">
                                <div class="col-md-3 col-6">
                                    <div
                                        class="border rounded p-2 bg-white shadow-sm h-100 d-flex flex-column justify-content-center">
                                        <div class="text-muted small">Ер тўла ҳолати</div>
                                        <div class="fw-bold mt-1">
                                            <i
                                                class="fas fa-{{ $yertola->does_exists_yer_tola ? 'check-circle text-success' : 'times-circle text-danger' }}"></i>
                                            {{ $yertola->does_exists_yer_tola ? 'Мавжуд' : 'Мавжуд эмас' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div
                                        class="border rounded p-2 bg-white shadow-sm h-100 d-flex flex-column justify-content-center">
                                        <div class="text-muted small">Фойдаланиш мумкинлиги</div>
                                        <div class="fw-bold mt-1">
                                            <i
                                                class="fas fa-{{ $yertola->does_can_we_use_yer_tola ? 'check-circle text-success' : 'times-circle text-danger' }}"></i>
                                            {{ $yertola->does_can_we_use_yer_tola ? 'Ҳа' : 'Йўқ' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div
                                        class="border rounded p-2 bg-white shadow-sm h-100 d-flex flex-column justify-content-center">
                                        <div class="text-muted small">Яратилган сана</div>
                                        <div class="fw-bold mt-1">
                                            <i class="far fa-calendar-alt text-primary me-1"></i>
                                            {{ $yertola->created_at->format('d.m.Y') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div
                                        class="border rounded p-2 bg-white shadow-sm h-100 d-flex flex-column justify-content-center">
                                        <div class="text-muted small">Бошқарув тури</div>
                                        <div class="fw-bold mt-1">
                                            <i
                                                class="fas fa-{{ isset($yertola->company_management) ? 'building text-primary' : 'user text-info' }} me-1"></i>
                                            {{ isset($yertola->company_management) ? 'Бошқарув сервис компанияси' : 'Шахс' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabs Navigation -->
                        <div class="px-3 pt-3">
                            <ul class="nav nav-tabs nav-fill" id="yerTolaTab{{ $yertola->id }}" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="info-tab{{ $yertola->id }}" data-bs-toggle="tab"
                                        data-bs-target="#info-content{{ $yertola->id }}" type="button" role="tab"
                                        aria-controls="info-content{{ $yertola->id }}" aria-selected="true">
                                        <i class="fas fa-info-circle me-1"></i> Асосий маълумот
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="gallery-tab{{ $yertola->id }}" data-bs-toggle="tab"
                                        data-bs-target="#gallery-content{{ $yertola->id }}" type="button" role="tab"
                                        aria-controls="gallery-content{{ $yertola->id }}" aria-selected="false">
                                        <i class="fas fa-images me-1"></i> Фотогалерея
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="map-tab{{ $yertola->id }}" data-bs-toggle="tab"
                                        data-bs-target="#map-content{{ $yertola->id }}" type="button" role="tab"
                                        aria-controls="map-content{{ $yertola->id }}" aria-selected="false">
                                        <i class="fas fa-map-marked-alt me-1"></i> Харита
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <!-- Tab Content -->
                        <div class="tab-content p-3" id="yerTolaTabContent{{ $yertola->id }}">
                            <!-- Information Tab -->
                            <div class="tab-pane fade show active" id="info-content{{ $yertola->id }}" role="tabpanel"
                                aria-labelledby="info-tab{{ $yertola->id }}">
                                <div class="row">
                                    <!-- Left Column -->
                                    <div class="col-lg-6">
                                        <!-- Address Section -->
                                        <div class="card mb-3 border-top border-3 border-primary shadow-sm">
                                            <div class="card-header bg-white">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-map-marker-alt text-primary fs-5 me-2"></i>
                                                    <h6 class="mb-0 fw-bold">Манзил маълумотлари</h6>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <table class="table table-bordered table-striped table-sm mb-0">
                                                    <tbody>
                                                        <tr>
                                                            <td class="text-muted" width="40%">Вилоят</td>
                                                            <td class="fw-medium">
                                                                {{ $yertola->subStreet->district->region->name_uz ?? 'Маълумот йўқ' }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">Туман</td>
                                                            <td class="fw-medium">
                                                                {{ $yertola->subStreet->district->name_uz ?? 'Маълумот йўқ' }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">Маҳалла</td>
                                                            <td class="fw-medium">
                                                                {{ $yertola->street->name ?? 'Маълумот йўқ' }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">Кўча</td>
                                                            <td class="fw-medium">
                                                                {{ $yertola->subStreet->name ?? 'Маълумот йўқ' }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">Уй рақами</td>
                                                            <td class="fw-medium">
                                                                {{ $yertola->home_number ?? 'Маълумот йўқ' }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <!-- Management Section -->
                                        <div class="card mb-3 border-top border-3 border-info shadow-sm">
                                            <div class="card-header bg-white">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-user-tie text-info fs-5 me-2"></i>
                                                    <h6 class="mb-0 fw-bold">Бошқарув маълумотлари</h6>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                @if (isset($yertola->company_management))
                                                    <table class="table table-bordered table-striped table-sm mb-0">
                                                        <tbody>
                                                            <tr>
                                                                <td class="text-muted" width="40%">Ташкилот номи</td>
                                                                <td class="fw-medium">
                                                                    {{ $yertola->company_management->organization ?? 'Маълумот йўқ' }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-muted">СТИР рақами</td>
                                                                <td class="fw-medium">
                                                                    {{ $yertola->company_management->inn ?? 'Маълумот йўқ' }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-muted">Туман</td>
                                                                <td class="fw-medium">
                                                                    {{ $yertola->company_management->district ?? 'Маълумот йўқ' }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-muted">Манзил</td>
                                                                <td class="fw-medium">
                                                                    {{ $yertola->company_management->address ?? 'Маълумот йўқ' }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-muted">Вакил</td>
                                                                <td class="fw-medium">
                                                                    {{ $yertola->company_management->representative ?? 'Маълумот йўқ' }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-muted">Телефон рақами</td>
                                                                <td class="fw-medium">
                                                                    {{ $yertola->company_management->phone ?? 'Маълумот йўқ' }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-muted">Хизмат телефони</td>
                                                                <td class="fw-medium">
                                                                    {{ $yertola->company_management->service_phone ?? 'Маълумот йўқ' }}
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                @else
                                                    <table class="table table-bordered table-striped table-sm mb-0">
                                                        <tbody>
                                                            <tr>
                                                                <td class="text-muted" width="40%">Балансга масъул шахс
                                                                </td>
                                                                <td class="fw-medium">
                                                                    {{ $yertola->balance_keeper ?? 'Маълумот йўқ' }}
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Right Column -->
                                    <div class="col-lg-6">
                                        <!-- Area & Financial Section -->
                                        <div class="card mb-3 border-top border-3 border-warning shadow-sm">
                                            <div class="card-header bg-white">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-ruler-combined text-warning fs-5 me-2"></i>
                                                    <h6 class="mb-0 fw-bold">Майдон ва молиявий маълумотлар</h6>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <table class="table table-bordered table-striped table-sm mb-0">
                                                    <tbody>
                                                        <tr>
                                                            <td class="text-muted" width="60%">Ертўланинг умумий
                                                                майдони
                                                            </td>
                                                            <td class="fw-medium text-end">
                                                                {{ $yertola->umumiy_maydoni_yer_tola ?? 'Маълумот йўқ' }}
                                                                м²</td>

                                                        </tr>
                                                        <tr>



                                                            <td class="text-muted" width="60%">Ижарага берилган қисм
                                                            </td>
                                                            <td class="fw-medium text-end">
                                                                {{ $yertola->ijaraga_berilgan_qismi_yer_tola ?? 'Маълумот йўқ' }}
                                                                м²</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">Ижарага берилмаган қисм</td>
                                                            <td class="fw-medium text-end">
                                                                {{ $yertola->ijaraga_berilmagan_qismi_yer_tola ?? 'Маълумот йўқ' }}
                                                                м²</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">Техник қисм</td>
                                                            <td class="fw-medium text-end">
                                                                {{ $yertola->texnik_qismi_yer_tola ?? 'Маълумот йўқ' }}
                                                                м²</td>
                                                        </tr>
                                                        <tr class="table-light">
                                                            <td class="fw-bold">Жами майдон</td>
                                                            <td class="fw-bold text-end">
                                                                @php
                                                                    $totalArea = 0;
                                                                    if ($yertola->ijaraga_berilgan_qismi_yer_tola) {
                                                                        $totalArea +=
                                                                            $yertola->ijaraga_berilgan_qismi_yer_tola;
                                                                    }
                                                                    if ($yertola->ijaraga_berilmagan_qismi_yer_tola) {
                                                                        $totalArea +=
                                                                            $yertola->ijaraga_berilmagan_qismi_yer_tola;
                                                                    }
                                                                    if ($yertola->texnik_qismi_yer_tola) {
                                                                        $totalArea += $yertola->texnik_qismi_yer_tola;
                                                                    }
                                                                    echo $totalArea > 0
                                                                        ? $totalArea . ' м²'
                                                                        : 'Маълумот йўқ';
                                                                @endphp
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">Ижарага берилган қисмини ойлик ижара
                                                                қиймати (сўм)</td>
                                                            <td class="fw-medium text-end">
                                                                @if ($yertola->oylik_ijara_narxi_yer_tola)
                                                                    <span
                                                                        class="text-success">{{ number_format($yertola->oylik_ijara_narxi_yer_tola, 0, ',', ' ') }}
                                                                        сум</span>
                                                                @else
                                                                    <span class="text-muted">Маълумот йўқ</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <!-- Activities Section -->
                                        <div class="card mb-3 border-top border-3 border-secondary shadow-sm">
                                            <div class="card-header bg-white">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-briefcase text-secondary fs-5 me-2"></i>
                                                    <h6 class="mb-0 fw-bold">Фаолият турлари</h6>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                @if ($yertola->faoliyat_turi)
                                                    <div class="d-flex flex-wrap gap-2">
                                                        @foreach (json_decode($yertola->faoliyat_turi, true) as $activity)
                                                            <span
                                                                class="badge bg-light text-dark border border-secondary p-2">
                                                                {{ $activity }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <div class="alert alert-light text-center mb-0">
                                                        <i class="fas fa-info-circle me-2"></i>Фаолият турлари
                                                        кўрсатилмаган
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Dates Section -->
                                        <div class="card mb-3 border-top border-3 border-dark shadow-sm">
                                            <div class="card-header bg-white">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-calendar-alt text-dark fs-5 me-2"></i>
                                                    <h6 class="mb-0 fw-bold">Сана маълумотлари</h6>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <table class="table table-bordered table-striped table-sm mb-0">
                                                    <tbody>
                                                        <tr>
                                                            <td class="text-muted" width="40%">Яратилган сана</td>
                                                            <td class="fw-medium">
                                                                {{ $yertola->created_at->format('d.m.Y') }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">Охирги таҳрир</td>
                                                            <td class="fw-medium">
                                                                {{ $yertola->updated_at->format('d.m.Y H:i') }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">Ҳужжат ҳолати</td>
                                                            <td class="fw-medium">
                                                                <span class="badge bg-success">Амалда</span>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Gallery Tab -->
                            <div class="tab-pane fade" id="gallery-content{{ $yertola->id }}" role="tabpanel"
                                aria-labelledby="gallery-tab{{ $yertola->id }}">
                                @if (isset($yertola->files) && count($yertola->files) > 0)
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">
                                                <i class="fas fa-images text-primary me-2"></i>
                                                Жами <span class="badge bg-primary">{{ count($yertola->files) }}</span> та
                                                фото
                                            </h6>
                                            <div>
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                    onclick="startSlideshow{{ $yertola->id }}()">
                                                    <i class="fas fa-play me-1"></i> Слайдшоу
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-3" id="gallery{{ $yertola->id }}">
                                        @foreach ($yertola->files as $index => $file)
                                            <div class="col-6 col-md-4 col-lg-3">
                                                <div class="card h-100 border-0 shadow-sm gallery-card">
                                                    <div class="gallery-img-container">
                                                        @if (strtolower(pathinfo($file->path, PATHINFO_EXTENSION)) === 'heic')
                                                            <img data-heic="{{ asset('storage/' . $file->path) }}"
                                                                class="heic-image card-img-top"
                                                                alt="Фото {{ $index + 1 }}">
                                                            <div class="overlay">
                                                                <div class="spinner-border text-light" role="status">
                                                                    <span class="visually-hidden">Юклаш...</span>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <img src="{{ asset('storage/' . $file->path) }}"
                                                                class="card-img-top" alt="Фото {{ $index + 1 }}">
                                                        @endif
                                                        <div class="img-overlay">
                                                            <a href="{{ asset('storage/' . $file->path) }}"
                                                                class="btn btn-light btn-sm rounded-circle glightbox-{{ $yertola->id }}"
                                                                data-gallery="yertola-gallery-{{ $yertola->id }}"
                                                                data-title="Ер тўла #{{ $yertola->id }} - Фото {{ $index + 1 }}"
                                                                data-description="Манзил: {{ $yertola->subStreet->name ?? '' }} {{ $yertola->home_number ?? '' }}">
                                                                <i class="fas fa-search-plus"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="card-footer bg-white text-center small">
                                                        Фото {{ $index + 1 }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="alert alert-info text-center">
                                        <i class="far fa-images fa-2x mb-3"></i>
                                        <p class="mb-0">Ушбу ер тўла учун расмлар мавжуд эмас</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Map Tab -->
                            <div class="tab-pane fade" id="map-content{{ $yertola->id }}" role="tabpanel"
                                aria-labelledby="map-tab{{ $yertola->id }}">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-white py-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">
                                                <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                                Объект жойлашуви
                                            </h6>
                                            <a href="https://www.google.com/maps?q={{ $yertola->latitude }},{{ $yertola->longitude }}"
                                                target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-external-link-alt me-1"></i> Google Map'да очиш
                                            </a>
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="ratio ratio-16x9">
                                            <iframe style="width:100%; height:400px"
                                                src="https://www.google.com/maps?q={{ $yertola->latitude }},{{ $yertola->longitude }}&output=embed"
                                                allowfullscreen loading="lazy"
                                                referrerpolicy="no-referrer-when-downgrade">
                                            </iframe>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-white">
                                        <div class="row g-2">
                                            <div class="col-md-6">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light">Кенглик</span>
                                                    <input type="text" class="form-control"
                                                        value="{{ $yertola->latitude }}" readonly>
                                                    <button class="btn btn-outline-secondary" type="button"
                                                        onclick="copyToClipboard('{{ $yertola->latitude }}')">
                                                        <i class="far fa-copy"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light">Узунлик</span>
                                                    <input type="text" class="form-control"
                                                        value="{{ $yertola->longitude }}" readonly>
                                                    <button class="btn btn-outline-secondary" type="button"
                                                        onclick="copyToClipboard('{{ $yertola->longitude }}')">
                                                        <i class="far fa-copy"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light">
                        <div class="d-flex justify-content-between w-100">
                            <div>
                                <span class="text-muted small">
                                    <i class="far fa-clock me-1"></i> Охирги янгиланиш:
                                    {{ $yertola->updated_at->format('d.m.Y H:i') }}
                                </span>
                            </div>
                            <div>
                                {{-- @can('edit', $yertola)
                                    <a href="{{ route('yertola.edit', $yertola->id) }}" class="btn btn-outline-warning">
                                        <i class="fas fa-edit me-1"></i> Таҳрирлаш
                                    </a>
                                @endcan --}}
                                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-1"></i> Ёпиш
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <div class="d-flex justify-content-center mt-4">

        {{ $yertolas->links() }}
    </div>

@endsection

@section('styles')
    <style>
        /* General Modal Styling */
        .modal-content {
            border-radius: 0.5rem;
        }

        .modal-header {
            border-top-left-radius: 0.5rem;
            border-top-right-radius: 0.5rem;
        }

        .modal-footer {
            border-bottom-left-radius: 0.5rem;
            border-bottom-right-radius: 0.5rem;
        }

        /* Tab Styling */
        .nav-tabs .nav-link {
            color: #6c757d;
            font-weight: 500;
            padding: 0.75rem 1rem;
            border-radius: 0;
            transition: all 0.2s ease;
        }

        .nav-tabs .nav-link.active {
            color: #0d6efd;
            font-weight: 600;
            border-bottom: 2px solid #0d6efd;
        }

        .nav-tabs .nav-link:hover:not(.active) {
            background-color: rgba(13, 110, 253, 0.05);
            border-color: transparent;
        }

        /* Gallery Styling */
        .gallery-img-container {
            position: relative;
            height: 180px;
            overflow: hidden;
        }

        .gallery-img-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .gallery-card:hover .gallery-img-container img {
            transform: scale(1.05);
        }

        .img-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.4);
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .gallery-card:hover .img-overlay {
            opacity: 1;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            background: rgba(0, 0, 0, 0.6);
        }

        .heic-image.loaded+.overlay {
            display: none;
        }

        /* Table Styling */
        .table-striped>tbody>tr:nth-of-type(odd)>* {
            background-color: rgba(0, 0, 0, 0.02);
        }

        .table-sm td {
            padding: 0.5rem;
        }

        /* Card Styling */
        .card {
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }

        /* Notification Animation */
        @keyframes notification-pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        .notification-pulse {
            animation: notification-pulse 1.5s infinite;
        }

        /* Print-friendly Styles */
        @media print {
            .modal-dialog {
                max-width: 100%;
                margin: 0;
            }

            .modal-content {
                border: none !important;
                box-shadow: none !important;
            }

            .btn,
            .nav-tabs,
            .tab-pane:not(.active) {
                display: none !important;
            }

            .tab-pane.active {
                display: block !important;
                opacity: 1 !important;
            }

            .card {
                break-inside: avoid;
            }
        }
    </style>
@endsection
@section('scripts')
    <!-- Include GLightbox JS -->
    <script src="https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/js/glightbox.min.js"></script>
    <!-- Include HEIC2ANY for HEIC image conversion -->
    <script src="https://cdn.jsdelivr.net/npm/heic2any@0.0.4/dist/heic2any.min.js"></script>

    <script>
        // Copy to clipboard function
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Create and show toast notification
                const toastElement = document.createElement('div');
                toastElement.className = 'position-fixed bottom-0 end-0 p-3';
                toastElement.style.zIndex = '11';
                toastElement.innerHTML = `
                    <div class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                <i class="fas fa-check-circle me-2"></i> Нусха кўчирилди!
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>
                `;
                document.body.appendChild(toastElement);

                const toast = new bootstrap.Toast(toastElement.querySelector('.toast'), {
                    delay: 2000
                });
                toast.show();

                // Remove toast after it's hidden
                toastElement.addEventListener('hidden.bs.toast', function() {
                    document.body.removeChild(toastElement);
                });
            }).catch(function(err) {
                console.error('Could not copy text: ', err);
            });
        }

        // Function to start slideshow for a specific gallery
        function startSlideshow(yertolaId) {
            const lightboxInstance = GLightbox({
                selector: `.glightbox[data-gallery="yertola-gallery-${yertolaId}"]`,
                touchNavigation: true,
                loop: true,
                autoplayVideos: true
            });

            lightboxInstance.open();

            // Start slideshow after 1 second
            setTimeout(() => {
                const slideInterval = setInterval(() => {
                    lightboxInstance.nextSlide();
                }, 3000);

                // Clear interval when lightbox is closed
                lightboxInstance.on('close', () => {
                    clearInterval(slideInterval);
                });
            }, 1000);
        }

        // Process HEIC images
        function processHeicImage(img) {
            const heicSrc = img.getAttribute('data-heic');
            if (!heicSrc) return;

            fetch(heicSrc)
                .then(res => res.blob())
                .then(blob => heic2any({
                    blob: blob,
                    toType: "image/jpeg",
                    quality: 0.8
                }))
                .then(jpegBlob => {
                    const url = URL.createObjectURL(jpegBlob);
                    img.src = url;
                    img.classList.add('loaded');

                    // Add this image to lightbox if it's in a gallery container
                    const container = img.closest('.gallery-img-container');
                    if (container) {
                        const galleryId = img.getAttribute('data-gallery-id');
                        let overlay = container.querySelector('.img-overlay');

                        if (!overlay) {
                            overlay = document.createElement('div');
                            overlay.className = 'img-overlay';
                            container.appendChild(overlay);
                        }

                        // Check if link already exists
                        if (!overlay.querySelector('a')) {
                            const link = document.createElement('a');
                            link.href = url;
                            link.className = 'btn btn-light btn-sm rounded-circle glightbox';
                            link.setAttribute('data-gallery', `yertola-gallery-${galleryId}`);
                            link.innerHTML = '<i class="fas fa-search-plus"></i>';

                            overlay.appendChild(link);

                            // Refresh GLightbox for this gallery
                            if (window[`lightbox${galleryId}`]) {
                                window[`lightbox${galleryId}`].reload();
                            }
                        }
                    }
                })
                .catch(err => {
                    console.error("Error converting HEIC image:", err);
                    img.src = '/images/error-image.jpg'; // Fallback image
                    img.classList.add('loaded');
                });
        }

        // Initialize GLightbox for a specific yertola
        function initGallery(yertolaId) {
            // Initialize GLightbox
            window[`lightbox${yertolaId}`] = GLightbox({
                selector: `.glightbox[data-gallery="yertola-gallery-${yertolaId}"]`,
                touchNavigation: true,
                loop: true,
                autoplayVideos: true,
                preload: false
            });

            // Process HEIC images in this modal
            document.querySelectorAll(`#detailsModal${yertolaId} .heic-image`).forEach(img => {
                img.setAttribute('data-gallery-id', yertolaId);
                processHeicImage(img);
            });
        }

        // Initialize all galleries when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Setup global listeners for modals
            document.querySelectorAll('[id^="detailsModal"]').forEach(modal => {
                const yertolaId = modal.id.replace('detailsModal', '');

                // Initialize when modal is shown
                modal.addEventListener('shown.bs.modal', function() {
                    // Init gallery
                    initGallery(yertolaId);

                    // Set focus on info tab
                    const infoTab = document.getElementById(`info-tab${yertolaId}`);
                    if (infoTab) infoTab.focus();
                });

                // Clean up when modal is hidden
                modal.addEventListener('hidden.bs.modal', function() {
                    // Dispose lightbox to prevent memory leaks
                    if (window[`lightbox${yertolaId}`]) {
                        window[`lightbox${yertolaId}`].destroy();
                        window[`lightbox${yertolaId}`] = null;
                    }
                });
            });

            // Make slideshow function available globally
            window.startSlideshow = startSlideshow;
        });

        // Print function for modal content
        function printModalContent(yertolaId) {
            const printContents = document.getElementById(`info-content${yertolaId}`).innerHTML;
            const originalContents = document.body.innerHTML;

            document.body.innerHTML = `
                <div class="container mt-4 print-container">
                    <h1 class="text-center mb-4">Ер Тўла #${yertolaId} - Маълумотлар</h1>
                    <div class="print-date text-end mb-3">
                        <small>Чоп этилган сана: ${new Date().toLocaleDateString('uz-UZ')} ${new Date().toLocaleTimeString('uz-UZ')}</small>
                    </div>
                    ${printContents}
                    <div class="mt-5 pt-3 border-top">
                        <p class="text-center text-muted small">
                            <em>Ушбу ҳужжат "Ер Тўла" маълумотлар тизимидан яратилди</em><br>
                            <em>Яратувчи: ${document.querySelector('meta[name="user-login"]')?.content || 'InvestUz'}</em>
                        </p>
                    </div>
                </div>
            `;

            window.print();
            document.body.innerHTML = originalContents;

            // Reinitialize modals after printing
            setTimeout(() => {
                location.reload();
            }, 1000);
        }

        // Export as PDF function (if needed)
        function exportAsPdf(yertolaId) {
            // Implementation for PDF export can be added here
            // This would typically use a library like jsPDF or html2pdf
            alert('PDF экспорт функцияси ишлаб чиқиш жараёнида');
        }
    </script>
@endsection
