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
            <table class="table table-hover text-center align-middle">
                <thead class="table-primary">
                    <style>
                        .table .td_address {
                            max-width: 200px;
                            word-wrap: break-word;
                            white-space: normal;
                        }
                    </style>
                    <tr>
                        <th>#</th>
                        <th class="td_address">📍 Манзил</th>
                        <th>🏠 Ер тўла</th>
                        <th>✅ Фойдаланиш</th>
                        <th>⚙ Амаллар</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($yertolas as $yertola)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="td_address">

                                {{ $yertola->subStreet->district->name_uz ?? 'Маълумот йўқ' }} Tumani,

                                {{ $yertola->street->name ?? 'Маълумот йўқ' }} Mfy,
                                {{ $yertola->subStreet->name ?? 'Маълумот йўқ' }},


                                {{ $yertola->home_number ?? 'Маълумот йўқ' }}</td>
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
                                {{-- <a href="{{ route('yertola.edit', $yertola->id) }}" class="btn btn-warning btn-sm">✏️
                                    Таҳрирлаш</a>

                                <form action="{{ route('yertola.destroy', $yertola) }}" method="POST"
                                    class="d-inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Ўчириш"
                                        onclick="return confirm('Сиз ростдан ҳам бу объектни ўчиришни истайсизми?');">
                                        🗑️ Ўчириш
                                    </button>
                                </form> --}}
                            </td>
                        </tr>

                        <!-- MODAL: Ер тўла тўлиқ маълумот -->
                        <div class="modal fade" id="detailsModal{{ $yertola->id }}" tabindex="-1"
                            aria-labelledby="modalLabel{{ $yertola->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title text-primary fw-bold" id="modalLabel{{ $yertola->id }}">📋
                                            Ер Тўла Маълумоти</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <ul class="list-group list-group-flush">
                                            <!-- Address Section -->
                                            <li class="list-group-item p-3">
                                                <div class="d-flex align-items-center mb-2">
                                                    <span class="badge rounded-pill bg-primary me-2">📍</span>
                                                    <strong class="fs-5">Манзил</strong>
                                                </div>
                                                <div class="mt-2 ps-3">
                                                    <div class="d-flex py-1 border-bottom border-light">
                                                        <span class="text-muted me-2"
                                                            style="min-width: 120px;">Вилоят:</span>
                                                        <span
                                                            class="fw-medium">{{ $yertola->subStreet->district->region->name_uz ?? 'Маълумот йўқ' }}</span>
                                                    </div>
                                                    <div class="d-flex py-1 border-bottom border-light">
                                                        <span class="text-muted me-2"
                                                            style="min-width: 120px;">Туман:</span>
                                                        <span
                                                            class="fw-medium">{{ $yertola->subStreet->district->name_uz ?? 'Маълумот йўқ' }}</span>
                                                    </div>
                                                    <div class="d-flex py-1 border-bottom border-light">
                                                        <span class="text-muted me-2"
                                                            style="min-width: 120px;">Маҳалла:</span>
                                                        <span
                                                            class="fw-medium">{{ $yertola->street->name ?? 'Маълумот йўқ' }}</span>
                                                    </div>
                                                    <div class="d-flex py-1 border-bottom border-light">
                                                        <span class="text-muted me-2" style="min-width: 120px;">Кўча:</span>
                                                        <span
                                                            class="fw-medium">{{ $yertola->subStreet->name ?? 'Маълумот йўқ' }}</span>
                                                    </div>
                                                    <div class="d-flex py-1">
                                                        <span class="text-muted me-2" style="min-width: 120px;">Уй
                                                            рақами:</span>
                                                        <span
                                                            class="fw-medium">{{ $yertola->home_number ?? 'Маълумот йўқ' }}</span>
                                                    </div>
                                                </div>
                                            </li>

                                            <!-- Basic Info Section -->
                                            <li class="list-group-item p-3">
                                                <div class="d-flex align-items-center mb-2">
                                                    <span class="badge rounded-pill bg-success me-2">🏠</span>
                                                    <strong class="fs-5">Умумий маълумот</strong>
                                                </div>
                                                <div class="mt-2 ps-3">
                                                    <div class="d-flex py-1 border-bottom border-light">
                                                        <span class="text-muted me-2" style="min-width: 220px;">Ер
                                                            тўла:</span>
                                                        <span
                                                            class="fw-medium">{{ $yertola->does_exists_yer_tola ? 'Мавжуд' : 'Мавжуд эмас' }}</span>
                                                    </div>
                                                    <div class="d-flex py-1 border-bottom border-light">
                                                        <span class="text-muted me-2" style="min-width: 220px;">Фойдаланиш
                                                            мумкин:</span>
                                                        <span
                                                            class="fw-medium">{{ $yertola->does_can_we_use_yer_tola ? 'Ҳа' : 'Йўқ' }}</span>
                                                    </div>
                                                    <div class="d-flex py-1">
                                                        <span class="text-muted me-2"
                                                            style="min-width: 220px;">Стихия:</span>
                                                        <span
                                                            class="fw-medium">{{ $yertola->stir ?? 'Маълумот йўқ' }}</span>
                                                    </div>
                                                </div>
                                            </li>

                                            <!-- Management Section -->
                                            <li class="list-group-item p-3">
                                                <div class="d-flex align-items-center mb-2">
                                                    <span class="badge rounded-pill bg-info me-2">👤</span>
                                                    <strong class="fs-5">Бошқарувчи</strong>
                                                </div>
                                                <div class="mt-2 ps-3">
                                                    @if (isset($yertola->company_management))
                                                        <div class="d-flex py-1 border-bottom border-light">
                                                            <span class="text-muted me-2" style="min-width: 150px;">Ташкилот
                                                                номи:</span>
                                                            <span
                                                                class="fw-medium">{{ $yertola->company_management->organization ?? 'Маълумот йўқ' }}</span>
                                                        </div>
                                                        <div class="d-flex py-1 border-bottom border-light">
                                                            <span class="text-muted me-2" style="min-width: 150px;">СТИР
                                                                рақами:</span>
                                                            <span
                                                                class="fw-medium">{{ $yertola->company_management->inn ?? 'Маълумот йўқ' }}</span>
                                                        </div>
                                                        <div class="d-flex py-1 border-bottom border-light">
                                                            <span class="text-muted me-2"
                                                                style="min-width: 150px;">Туман:</span>
                                                            <span
                                                                class="fw-medium">{{ $yertola->company_management->district ?? 'Маълумот йўқ' }}</span>
                                                        </div>
                                                        <div class="d-flex py-1 border-bottom border-light">
                                                            <span class="text-muted me-2"
                                                                style="min-width: 150px;">Манзил:</span>
                                                            <span
                                                                class="fw-medium">{{ $yertola->company_management->address ?? 'Маълумот йўқ' }}</span>
                                                        </div>
                                                        <div class="d-flex py-1 border-bottom border-light">
                                                            <span class="text-muted me-2"
                                                                style="min-width: 150px;">Вакил:</span>
                                                            <span
                                                                class="fw-medium">{{ $yertola->company_management->representative ?? 'Маълумот йўқ' }}</span>
                                                        </div>
                                                        <div class="d-flex py-1 border-bottom border-light">
                                                            <span class="text-muted me-2" style="min-width: 150px;">Телефон
                                                                рақами:</span>
                                                            <span
                                                                class="fw-medium">{{ $yertola->company_management->phone ?? 'Маълумот йўқ' }}</span>
                                                        </div>
                                                        <div class="d-flex py-1">
                                                            <span class="text-muted me-2" style="min-width: 150px;">Хизмат
                                                                телефони:</span>
                                                            <span
                                                                class="fw-medium">{{ $yertola->company_management->service_phone ?? 'Маълумот йўқ' }}</span>
                                                        </div>
                                                    @else
                                                        <div class="d-flex py-1">
                                                            <span class="text-muted me-2"
                                                                style="min-width: 150px;">Балансга масъул шахс:</span>
                                                            <span
                                                                class="fw-medium">{{ $yertola->balance_keeper ?? 'Маълумот йўқ' }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </li>

                                            <!-- Area Section -->
                                            <li class="list-group-item p-3">
                                                <div class="d-flex align-items-center mb-2">
                                                    <span class="badge rounded-pill bg-warning text-dark me-2">📏</span>
                                                    <strong class="fs-5">Майдон маълумотлари</strong>
                                                </div>
                                                <div class="mt-2 ps-3">
                                                    <div class="d-flex py-1 border-bottom border-light">
                                                        <span class="text-muted me-2" style="min-width: 220px;">Ижарага
                                                            берилган қисм:</span>
                                                        <span
                                                            class="fw-medium">{{ $yertola->ijaraga_berilgan_qismi_yer_tola ?? 'Маълумот йўқ' }}
                                                            м²</span>
                                                    </div>
                                                    <div class="d-flex py-1 border-bottom border-light">
                                                        <span class="text-muted me-2" style="min-width: 220px;">Ижарага
                                                            берилмаган қисм:</span>
                                                        <span
                                                            class="fw-medium">{{ $yertola->ijaraga_berilmagan_qismi_yer_tola ?? 'Маълумот йўқ' }}
                                                            м²</span>
                                                    </div>
                                                    <div class="d-flex py-1">
                                                        <span class="text-muted me-2" style="min-width: 220px;">Техник
                                                            қисм:</span>
                                                        <span
                                                            class="fw-medium">{{ $yertola->texnik_qismi_yer_tola ?? 'Маълумот йўқ' }}
                                                            м²</span>
                                                    </div>
                                                </div>
                                            </li>

                                            <!-- Financial Section -->
                                            <li class="list-group-item p-3">
                                                <div class="d-flex align-items-center mb-2">
                                                    <span class="badge rounded-pill bg-danger me-2">💰</span>
                                                    <strong class="fs-5">Молиявий маълумот</strong>
                                                </div>
                                                <div class="mt-2 ps-3">
                                                    <div class="d-flex py-1">
                                                        <span class="text-muted me-2" style="min-width: 150px;">Ойлик
                                                            ижара нархи:</span>
                                                        <span
                                                            class="fw-medium">{{ number_format($yertola->oylik_ijara_narxi_yer_tola, 0, ',', ' ') ?? 'Маълумот йўқ' }}
                                                            сум</span>
                                                    </div>
                                                </div>
                                            </li>

                                            <!-- Activities Section -->
                                            <li class="list-group-item p-3">
                                                <div class="d-flex align-items-center mb-2">
                                                    <span class="badge rounded-pill bg-secondary me-2">🏢</span>
                                                    <strong class="fs-5">Фаолият тури</strong>
                                                </div>
                                                <div class="mt-2 ps-3">
                                                    @if ($yertola->faoliyat_turi)
                                                        <div class="d-flex flex-wrap gap-1">
                                                            @foreach (json_decode($yertola->faoliyat_turi, true) as $activity)
                                                                <span
                                                                    class="badge bg-light text-dark border">{{ $activity }}</span>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <div class="text-muted">Маълумот йўқ</div>
                                                    @endif
                                                </div>
                                            </li>

                                            <!-- Dates Section -->
                                            <li class="list-group-item p-3">
                                                <div class="d-flex align-items-center mb-2">
                                                    <span class="badge rounded-pill bg-dark me-2">📆</span>
                                                    <strong class="fs-5">Сана маълумотлари</strong>
                                                </div>
                                                <div class="mt-2 ps-3">
                                                    <div class="d-flex py-1 border-bottom border-light">
                                                        <span class="text-muted me-2" style="min-width: 150px;">Яратилган
                                                            сана:</span>
                                                        <span
                                                            class="fw-medium">{{ $yertola->created_at->format('d.m.Y') }}</span>
                                                    </div>
                                                    <div class="d-flex py-1">
                                                        <span class="text-muted me-2" style="min-width: 150px;">Охирги
                                                            таҳрир:</span>
                                                        <span
                                                            class="fw-medium">{{ $yertola->updated_at->format('d.m.Y H:i') }}</span>
                                                    </div>
                                                </div>
                                            </li>

                                            <!-- Gallery Section -->
                                            <li class="list-group-item p-3">
                                                <div class="d-flex align-items-center mb-3">
                                                    <span class="badge rounded-pill bg-primary me-2">📸</span>
                                                    <strong class="fs-5">Фотосурат галереяси</strong>
                                                </div>
                                                <div class="gallery-container">
                                                    <div class="row g-2">
{{-- @dd($yertola->files) --}}
                                                        @if (isset($yertola->files) && count($yertola->files) > 0)
                                                            @foreach ($yertola->files as $file)
                                                                <div class="col-6 col-md-4 col-lg-3">
                                                                    <div class="gallery-item rounded overflow-hidden">
                                                                        @if (strtolower(pathinfo($file->path, PATHINFO_EXTENSION)) === 'heic')
                                                                            <!-- HEIC images will be converted using HEIC2ANY -->
                                                                            <a href="javascript:void(0);"
                                                                                class="heic-container">
                                                                                <img data-heic="{{ asset('storage/' . $file->path) }}"
                                                                                    class="heic-image img-fluid"
                                                                                    alt="Image">
                                                                                <div
                                                                                    class="overlay d-flex justify-content-center align-items-center">
                                                                                    <div class="spinner-border text-light"
                                                                                        role="status">
                                                                                        <span
                                                                                            class="visually-hidden">Loading...</span>
                                                                                    </div>
                                                                                </div>
                                                                            </a>
                                                                        @else
                                                                            <!-- Display non-HEIC images directly -->
                                                                            <a href="{{ asset('storage/' . $file->path) }}"
                                                                                class="glightbox"
                                                                                data-gallery="yertola-gallery"
                                                                                data-title="Ер тўла"
                                                                                data-description="Манзил: {{ $yertola->subStreet->name ?? '' }} {{ $yertola->home_number ?? '' }}">
                                                                                <img src="{{ asset('storage/' . $file->path) }}"
                                                                                    class="img-fluid" alt="Image">
                                                                                <div
                                                                                    class="overlay d-flex justify-content-center align-items-center">
                                                                                    <i
                                                                                        class="fas fa-search-plus text-light fs-4"></i>
                                                                                </div>
                                                                            </a>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            <div class="col-12">
                                                                <div class="alert alert-light text-center">
                                                                    <i class="far fa-images me-2"></i>Расмлар мавжуд эмас
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </li>

                                            <!-- Map Section -->
                                            <li class="list-group-item p-3">
                                                <div class="d-flex align-items-center mb-3">
                                                    <span class="badge rounded-pill bg-success me-2">🗺️</span>
                                                    <strong class="fs-5">Харита</strong>
                                                </div>
                                                <div class="map-container rounded overflow-hidden">
                                                    <iframe
                                                        src="https://www.google.com/maps?q={{ $yertola->latitude }},{{ $yertola->longitude }}&output=embed"
                                                        width="100%" height="450" style="border:0;" allowfullscreen
                                                        loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                                                    </iframe>
                                                </div>
                                            </li>
                                        </ul>
                                        <style>
                                            .modal-body .list-group-item {
                                                transition: all 0.3s ease;
                                            }

                                            .modal-body .list-group-item:hover {
                                                background-color: rgba(0, 0, 0, 0.01);
                                            }

                                            .gallery-item {
                                                position: relative;
                                                height: 150px;
                                                overflow: hidden;
                                                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
                                                transition: all 0.3s ease;
                                            }

                                            .gallery-item:hover {
                                                transform: translateY(-3px);
                                                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
                                            }

                                            .gallery-item img {
                                                width: 100%;
                                                height: 100%;
                                                object-fit: cover;
                                                transition: transform 0.5s ease;
                                            }

                                            .gallery-item:hover img {
                                                transform: scale(1.05);
                                            }

                                            .gallery-item .overlay {
                                                position: absolute;
                                                top: 0;
                                                left: 0;
                                                right: 0;
                                                bottom: 0;
                                                background-color: rgba(0, 0, 0, 0.4);
                                                opacity: 0;
                                                transition: opacity 0.3s ease;
                                            }

                                            .gallery-item:hover .overlay {
                                                opacity: 1;
                                            }

                                            .map-container {
                                                box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
                                            }

                                            /* Custom styling for HEIC images */
                                            .heic-container {
                                                display: block;
                                                position: relative;
                                                height: 100%;
                                            }

                                            .heic-image.loaded+.overlay {
                                                opacity: 0;
                                            }

                                            .heic-image.loaded+.overlay:hover {
                                                opacity: 1;
                                            }
                                        </style>
                                        <script>
                                            document.addEventListener('DOMContentLoaded', function() {
                                                // Initialize GLightbox
                                                const lightbox = GLightbox({
                                                    selector: '.glightbox',
                                                    touchNavigation: true,
                                                    loop: true,
                                                    autoplayVideos: true
                                                });

                                                // Handle HEIC images if any
                                                document.querySelectorAll('.heic-image').forEach(function(img) {
                                                    const heicSrc = img.getAttribute('data-heic');
                                                    if (heicSrc) {
                                                        // Using heic2any library to convert HEIC to JPEG
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

                                                                // Update parent link to make it openable in lightbox
                                                                const container = img.closest('.heic-container');
                                                                if (container) {
                                                                    container.href = url;
                                                                    container.classList.add('glightbox');
                                                                    container.setAttribute('data-gallery', 'yertola-gallery');

                                                                    // Refresh GLightbox to include this new item
                                                                    lightbox.reload();
                                                                }
                                                            })
                                                            .catch(err => {
                                                                console.error("Error converting HEIC image:", err);
                                                                img.src = '/images/error-image.jpg'; // Fallback image
                                                            });
                                                    }
                                                });
                                            });
                                        </script>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">❌
                                            Ёпиш</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /MODAL -->
                    @endforeach
                </tbody>
            </table>

            @if ($yertolas->isEmpty())
                <div class="text-center text-muted py-4">
                    🚫 Ҳеч қандай ер тўла топилмади.
                </div>
            @endif
        </div>
    </div>
@endsection
