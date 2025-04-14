@extends('layouts.admin')

@section('content')
    <div class="container-fluid px-4 py-3">
        <!-- Сарлавҳа ва Фильтрлар -->
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center mb-4">
            <h2 class="text-primary mb-3 mb-lg-0">
                <i class="fas fa-chart-bar me-2"></i> Мониторинг Статистикаси
            </h2>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <form id="filterForm" class="row g-2 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label small mb-1">Вилоят</label>
                            <select id="region_filter" class="form-select form-select-sm">
                                <option value="">Барча вилоятлар</option>
                                @foreach ($regions as $region)
                                    <option value="{{ $region->id }}">{{ $region->name_uz }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small mb-1">Туман</label>
                            <select id="district_filter" class="form-select form-select-sm">
                                <option value="">Барча туманлар</option>
                                @foreach ($districts as $district)
                                    <option value="{{ $district->id }}" data-region="{{ $district->region_id }}">
                                        {{ $district->name_uz }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i class="fas fa-filter me-1"></i> Фильтрлаш
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Умумий маълумот карточкалари -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-primary-subtle text-primary rounded p-3 me-3">
                                <i class="fas fa-building fa-2x"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0 small">Жами объектлар</h6>
                                <h3 class="mb-0 fw-bold" id="total_records">
                                    {{ number_format($totalAktivs + $totalYertolas) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-success-subtle text-success rounded p-3 me-3">
                                <i class="fas fa-store fa-2x"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0 small">Активлар</h6>
                                <h3 class="mb-0 fw-bold" id="total_aktivs">{{ number_format($totalAktivs) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-warning-subtle text-warning rounded p-3 me-3">
                                <i class="fas fa-warehouse fa-2x"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0 small">Ер тўлалар</h6>
                                <h3 class="mb-0 fw-bold" id="total_yertolas">{{ number_format($totalYertolas) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-info-subtle text-info rounded p-3 me-3">
                                <i class="fas fa-chart-pie fa-2x"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0 small">Тўлдирилиш даражаси</h6>
                                <h3 class="mb-0 fw-bold">
                                    @php
                                        $completionRate = 0;
                                        $totalObjects = $totalAktivs + $totalYertolas;
                                        $withData =
                                            ($aktivsWithPhotos->with_photos ?? 0) +
                                            ($yertolasWithPhotos->with_photos ?? 0);
                                        if ($totalObjects > 0) {
                                            $completionRate = round(($withData / $totalObjects) * 100);
                                        }
                                    @endphp
                                    {{ $completionRate }}%
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Активлар графиклари -->
        <div class="row mb-4">
            <div class="col-12">
                <h4 class="mb-3 text-primary border-bottom pb-2">
                    <i class="fas fa-store me-2"></i> Активлар статистикаси
                </h4>
            </div>

            <!-- Туманлар бўйича активлар -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-light py-3">
                        <h5 class="mb-0 fw-bold">Туманлар бўйича активлар</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="aktivsByDistrictChart" width="400" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- Вилоятлар бўйича активлар -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-light py-3">
                        <h5 class="mb-0 fw-bold">Вилоятлар бўйича активлар</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="aktivsByRegionChart" width="400" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- Активлар ҳолати -->
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-light py-3">
                        <h5 class="mb-0 fw-bold">Активлар фаолияти ҳолати</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="aktivsStatusChart" width="400" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- Ҳужжатлар ҳолати -->
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-light py-3">
                        <h5 class="mb-0 fw-bold">Ҳужжатлар мавжудлиги</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="dokumentlarChart" width="400" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- 24/7 режими -->
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-light py-3">
                        <h5 class="mb-0 fw-bold">24/7 режимида ишлаш</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="workingHoursChart" width="400" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ер тўлалар графиклари -->
        <div class="row">
            <div class="col-12">
                <h4 class="mb-3 text-primary border-bottom pb-2">
                    <i class="fas fa-warehouse me-2"></i> Ер тўлалар статистикаси
                </h4>
            </div>

            <!-- Туманлар бўйича ер тўлалар -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-light py-3">
                        <h5 class="mb-0 fw-bold">Туманлар бўйича ер тўлалар</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="yertolasByDistrictChart" width="400" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- Вилоятлар бўйича ер тўлалар -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-light py-3">
                        <h5 class="mb-0 fw-bold">Вилоятлар бўйича ер тўлалар</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="yertolasByRegionChart" width="400" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- Ер тўла ҳолати -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-light py-3">
                        <h5 class="mb-0 fw-bold">Ер тўла ҳолати</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="yertolaStatusChart" width="400" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- Ер тўла майдонлари -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-light py-3">
                        <h5 class="mb-0 fw-bold">Ер тўла майдонлари (ўртача)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="yertolaAreasChart" width="400" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Расмлар ва фотоҳужжатлар статистикаси -->
        <div class="row">
            <div class="col-12">
                <h4 class="mb-3 text-primary border-bottom pb-2">
                    <i class="fas fa-images me-2"></i> Расмлар ва фотоҳужжатлар
                </h4>
            </div>

            <!-- Активлар фото статистикаси -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-light py-3">
                        <h5 class="mb-0 fw-bold">Активлар фото статистикаси</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="aktivsFotoChart" width="400" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- Ер тўла фото статистикаси -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-light py-3">
                        <h5 class="mb-0 fw-bold">Ер тўла фото статистикаси</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="yertolasFotoChart" width="400" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
        }

        .icon-box {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bg-primary-subtle {
            background-color: rgba(13, 110, 253, 0.15);
        }

        .bg-success-subtle {
            background-color: rgba(25, 135, 84, 0.15);
        }

        .bg-warning-subtle {
            background-color: rgba(255, 193, 7, 0.15);
        }

        .bg-info-subtle {
            background-color: rgba(13, 202, 240, 0.15);
        }

        .rounded-4 {
            border-radius: 0.75rem !important;
        }

        canvas {
            max-width: 100%;
        }

        /* Responsive font sizes */
        @media (max-width: 768px) {
            h3 {
                font-size: 1.5rem;
            }

            h4 {
                font-size: 1.25rem;
            }

            h5 {
                font-size: 1.1rem;
            }
        }
    </style>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // CSRF token setup for AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Filter district dropdown based on selected region
            $('#region_filter').change(function() {
                var regionId = $(this).val();
                var districtDropdown = $('#district_filter');

                // Reset district dropdown
                districtDropdown.html('<option value="">Барча туманлар</option>');

                if (regionId) {
                    // Add only districts belonging to selected region
                    $('#district_filter option[data-region]').each(function() {
                        if ($(this).data('region') == regionId) {
                            districtDropdown.append($(this).clone());
                        }
                    });
                } else {
                    // Add all districts if no region selected
                    $('#district_filter option[data-region]').each(function() {
                        districtDropdown.append($(this).clone());
                    });
                }
            });

            // Form submission for filtering
            $('#filterForm').submit(function(e) {
                e.preventDefault();

                $.ajax({
                    url: '{{ route('statistics.filteredData') }}',
                    type: 'POST',
                    data: {
                        region_id: $('#region_filter').val(),
                        district_id: $('#district_filter').val(),
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        // Update counters
                        $('#total_aktivs').text(response.totalAktivs.toLocaleString('ru-RU'));
                        $('#total_yertolas').text(response.totalYertolas.toLocaleString(
                            'ru-RU'));
                        $('#total_records').text((response.totalAktivs + response.totalYertolas)
                            .toLocaleString('ru-RU'));

                        // Update charts (this would need to be implemented for each chart)
                        updateChartsWithFilteredData(response);
                    },
                    error: function(error) {
                        console.error('Error fetching filtered data:', error);
                        alert('Хатолик юз берди. Илтимос, қайта уриниб кўринг.');
                    }
                });
            });

            // Function to update charts based on filtered data
            function updateChartsWithFilteredData(data) {
                // This would update each chart with new data
                // Implementation would depend on the structure of your response
                // Example for updating a specific chart:

                // Example: Update aktiv status chart
                if (window.aktivsStatusChart && data.aktivsStatusDistribution) {
                    const statusLabels = data.aktivsStatusDistribution.map(item =>
                        item.faoliyat_xolati ? item.faoliyat_xolati : 'Номаълум');
                    const statusData = data.aktivsStatusDistribution.map(item => item.total);

                    window.aktivsStatusChart.data.labels = statusLabels;
                    window.aktivsStatusChart.data.datasets[0].data = statusData;
                    window.aktivsStatusChart.update();
                }

                // Additional chart updates would be added here
            }

            // Chart configuration and rendering

            // Colors for charts
            const chartColors = [
                'rgba(54, 162, 235, 0.7)', // Blue
                'rgba(255, 99, 132, 0.7)', // Red
                'rgba(75, 192, 192, 0.7)', // Green
                'rgba(255, 206, 86, 0.7)', // Yellow
                'rgba(153, 102, 255, 0.7)', // Purple
                'rgba(255, 159, 64, 0.7)', // Orange
                'rgba(199, 199, 199, 0.7)', // Gray
                'rgba(83, 102, 255, 0.7)', // Indigo
                'rgba(255, 99, 71, 0.7)', // Tomato
                'rgba(60, 179, 113, 0.7)' // Medium Sea Green
            ];

            // 1. Активлар по районам
            const aktivsByDistrictCtx = document.getElementById('aktivsByDistrictChart').getContext('2d');
            const aktivsByDistrictChart = new Chart(aktivsByDistrictCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($aktivsByDistrict->pluck('district_name')) !!},
                    datasets: [{
                        label: 'Активлар сони',
                        data: {!! json_encode($aktivsByDistrict->pluck('total')) !!},
                        backgroundColor: chartColors,
                        borderColor: chartColors.map(color => color.replace('0.7', '1')),
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.raw.toLocaleString(
                                        'ru-RU');
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString('ru-RU');
                                }
                            }
                        }
                    }
                }
            });

            // 2. Активлар по регионам
            const aktivsByRegionCtx = document.getElementById('aktivsByRegionChart').getContext('2d');
            const aktivsByRegionChart = new Chart(aktivsByRegionCtx, {
                type: 'pie',
                data: {
                    labels: {!! json_encode($aktivsByRegion->pluck('region_name')) !!},
                    datasets: [{
                        data: {!! json_encode($aktivsByRegion->pluck('total')) !!},
                        backgroundColor: chartColors,
                        borderColor: chartColors.map(color => color.replace('0.7', '1')),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 15,
                                padding: 15
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + context.raw.toLocaleString('ru-RU') +
                                        ' (' +
                                        Math.round((context.raw / context.dataset.data.reduce((a, b) =>
                                            a + b, 0)) * 100) + '%)';
                                }
                            }
                        }
                    }
                }
            });

            // 3. Статус активов
            const aktivsStatusLabels = {!! json_encode($aktivsStatusDistribution->pluck('faoliyat_xolati')) !!}.map(status =>
                status ? status : 'Номаълум');
            const aktivsStatusCtx = document.getElementById('aktivsStatusChart').getContext('2d');
            window.aktivsStatusChart = new Chart(aktivsStatusCtx, {
                type: 'doughnut',
                data: {
                    labels: aktivsStatusLabels,
                    datasets: [{
                        data: {!! json_encode($aktivsStatusDistribution->pluck('total')) !!},
                        backgroundColor: chartColors,
                        borderColor: chartColors.map(color => color.replace('0.7', '1')),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 15,
                                padding: 15
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + context.raw.toLocaleString('ru-RU') +
                                        ' (' +
                                        Math.round((context.raw / context.dataset.data.reduce((a, b) =>
                                            a + b, 0)) * 100) + '%)';
                                }
                            }
                        }
                    }
                }
            });

            // 4. Документы
            const dokumentlarCtx = document.getElementById('dokumentlarChart').getContext('2d');
            const dokumentlarChart = new Chart(dokumentlarCtx, {
                type: 'pie',
                data: {
                    labels: ['Ҳужжатлар мавжуд', 'Ҳужжатлар мавжуд эмас'],
                    datasets: [{
                        data: [
                            {{ $aktivsWithDocuments->with_documents ?? 0 }},
                            {{ $aktivsWithDocuments->without_documents ?? 0 }}
                        ],
                        backgroundColor: [chartColors[2], chartColors[1]],
                        borderColor: [chartColors[2].replace('0.7', '1'), chartColors[1].replace(
                            '0.7', '1')],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 15,
                                padding: 15
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + context.raw.toLocaleString('ru-RU') +
                                        ' (' +
                                        Math.round((context.raw / context.dataset.data.reduce((a, b) =>
                                            a + b, 0)) * 100) + '%)';
                                }
                            }
                        }
                    }
                }
            });

            // 5. Режим работы 24/7
            const workingHoursCtx = document.getElementById('workingHoursChart').getContext('2d');
            const workingHoursChart = new Chart(workingHoursCtx, {
                type: 'pie',
                data: {
                    labels: ['24/7 режими', 'Стандарт режим'],
                    datasets: [{
                        data: [
                            {{ $aktivs24_7Distribution->where('working_24_7', 1)->first()->total ?? 0 }},
                            {{ $aktivs24_7Distribution->where('working_24_7', 0)->first()->total ?? 0 }}
                        ],
                        backgroundColor: [chartColors[0], chartColors[6]],
                        borderColor: [chartColors[0].replace('0.7', '1'), chartColors[6].replace(
                            '0.7', '1')],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 15,
                                padding: 15
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + context.raw.toLocaleString('ru-RU') +
                                        ' (' +
                                        Math.round((context.raw / context.dataset.data.reduce((a, b) =>
                                            a + b, 0)) * 100) + '%)';
                                }
                            }
                        }
                    }
                }
            });

            // 6. Ер тўлалар по районам
            const yertolasByDistrictCtx = document.getElementById('yertolasByDistrictChart').getContext('2d');
            const yertolasByDistrictChart = new Chart(yertolasByDistrictCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($yertolasByDistrict->pluck('district_name')) !!},
                    datasets: [{
                        label: 'Ер тўлалар сони',
                        data: {!! json_encode($yertolasByDistrict->pluck('total')) !!},
                        backgroundColor: chartColors[3],
                        borderColor: chartColors[3].replace('0.7', '1'),
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.raw.toLocaleString(
                                        'ru-RU');
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString('ru-RU');
                                }
                            }
                        }
                    }
                }
            });

            // 7. Ер тўлалар по регионам
            const yertolasByRegionCtx = document.getElementById('yertolasByRegionChart').getContext('2d');
            const yertolasByRegionChart = new Chart(yertolasByRegionCtx, {
                type: 'pie',
                data: {
                    labels: {!! json_encode($yertolasByRegion->pluck('region_name')) !!},
                    datasets: [{
                        data: {!! json_encode($yertolasByRegion->pluck('total')) !!},
                        backgroundColor: chartColors,
                        borderColor: chartColors.map(color => color.replace('0.7', '1')),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 15,
                                padding: 15
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + context.raw.toLocaleString('ru-RU') +
                                        ' (' +
                                        Math.round((context.raw / context.dataset.data.reduce((a, b) =>
                                            a + b, 0)) * 100) + '%)';
                                }
                            }
                        }
                    }
                }
            });

            // 8. Статус ер тўла
            const yertolaStatusCtx = document.getElementById('yertolaStatusChart').getContext('2d');
            const yertolaStatusChart = new Chart(yertolaStatusCtx, {
                type: 'bar',
                data: {
                    labels: ['Мавжуд', 'Фойдаланиш мумкин', 'Ижарага берилган'],
                    datasets: [{
                        label: 'Ер тўлалар сони',
                        data: [
                            {{ $yertolaUsageStats->exists_count ?? 0 }},
                            {{ $yertolaUsageStats->can_use_count ?? 0 }},
                            {{ $yertolaUsageStats->rented_count ?? 0 }}
                        ],
                        backgroundColor: [chartColors[2], chartColors[0], chartColors[4]],
                        borderColor: [
                            chartColors[2].replace('0.7', '1'),
                            chartColors[0].replace('0.7', '1'),
                            chartColors[4].replace('0.7', '1')
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = {{ $yertolaUsageStats->total ?? 0 }};
                                    const percentage = total > 0 ? Math.round((context.raw / total) *
                                        100) : 0;
                                    return context.dataset.label + ': ' + context.raw.toLocaleString(
                                        'ru-RU') + ' (' + percentage + '%)';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString('ru-RU');
                                }
                            }
                        }
                    }
                }
            });

            // 9. Ер тўла площади
            const yertolaAreasCtx = document.getElementById('yertolaAreasChart').getContext('2d');
            const yertolaAreasChart = new Chart(yertolaAreasCtx, {
                type: 'polarArea',
                data: {
                    labels: ['Ижарага берилган', 'Ижарага берилмаган', 'Техник майдон'],
                    datasets: [{
                        data: [
                            {{ $yertolaAverages->avg_rented_area ?? 0 }},
                            {{ $yertolaAverages->avg_not_rented_area ?? 0 }},
                            {{ $yertolaAverages->avg_technical_area ?? 0 }}
                        ],
                        backgroundColor: [chartColors[2], chartColors[1], chartColors[6]],
                        borderColor: [
                            chartColors[2].replace('0.7', '1'),
                            chartColors[1].replace('0.7', '1'),
                            chartColors[6].replace('0.7', '1')
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 15,
                                padding: 15
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + context.raw.toFixed(1) + ' м²';
                                }
                            }
                        }
                    },
                    scales: {
                        r: {
                            ticks: {
                                callback: function(value) {
                                    return value.toFixed(1) + ' м²';
                                }
                            }
                        }
                    }
                }
            });

            // 10. Активлар фото статистикаси
            const aktivsFotoCtx = document.getElementById('aktivsFotoChart').getContext('2d');
            const aktivsFotoChart = new Chart(aktivsFotoCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Расмлар мавжуд', 'Расмлар мавжуд эмас'],
                    datasets: [{
                        data: [
                            {{ $aktivsWithPhotos->with_photos ?? 0 }},
                            {{ $aktivsWithPhotos->without_photos ?? 0 }}
                        ],
                        backgroundColor: [chartColors[2], chartColors[1]],
                        borderColor: [chartColors[2].replace('0.7', '1'), chartColors[1].replace(
                            '0.7', '1')],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 15,
                                padding: 15
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + context.raw.toLocaleString('ru-RU') +
                                        ' (' +
                                        Math.round((context.raw / context.dataset.data.reduce((a, b) =>
                                            a + b, 0)) * 100) + '%)';
                                }
                            }
                        }
                    }
                }
            });

            // 11. Ер тўла фото статистикаси
            const yertolasFotoCtx = document.getElementById('yertolasFotoChart').getContext('2d');
            const yertolasFotoChart = new Chart(yertolasFotoCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Расмлар мавжуд', 'Расмлар мавжуд эмас'],
                    datasets: [{
                        data: [
                            {{ $yertolasWithPhotos->with_photos ?? 0 }},
                            {{ $yertolasWithPhotos->without_photos ?? 0 }}
                        ],
                        backgroundColor: [chartColors[2], chartColors[1]],
                        borderColor: [chartColors[2].replace('0.7', '1'), chartColors[1].replace(
                            '0.7', '1')],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 15,
                                padding: 15
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + context.raw.toLocaleString('ru-RU') +
                                        ' (' +
                                        Math.round((context.raw / context.dataset.data.reduce((a, b) =>
                                            a + b, 0)) * 100) + '%)';
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
