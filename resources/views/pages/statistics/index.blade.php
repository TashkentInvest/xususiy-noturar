@extends('layouts.admin')
@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Бошқарув панелига хуш келибсиз</h4>
        </div>
        <div class="d-flex align-items-center flex-wrap text-nowrap">
            <div class="input-group date datepicker dashboard-date mr-2 mb-2 mb-md-0 d-md-none d-xl-flex" id="dashboardDate">
                <span class="input-group-addon bg-transparent"><i data-feather="calendar" class=" text-primary"></i></span>
                <input type="text" class="form-control">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-4">
                                <h6>Жами активлар сони</h6>
                                <h3>{{ $totalAktivs }}</h3>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-4">
                                <h6>24/7 ишлайдиган активлар</h6>
                                <h3>{{ $working247Aktivs }}</h3>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-4">
                                <h6>Бино турлари бўйича активлар</h6>
                                <ul>
                                    <li>Кўп қаватли уйлар: {{ $buildingTypeCounts['kopQavatliUy'] ?? 0 }}</li>
                                    <li>Алоҳида савдо дўконлари: {{ $buildingTypeCounts['AlohidaSavdoDokoni'] ?? 0 }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div id="buildingTypeChart"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Туманлар бўйича активлар сони</h6>
                    <div id="districtBarChart"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const districts = @json($districts);

            // Chart for districts and aktivs
            const districtOptions = {
                chart: {
                    type: 'bar',
                    height: 550
                },
                series: [{
                    name: 'Активлар',
                    data: districts.map(d => d.aktives_count)
                }],
                xaxis: {
                    categories: districts.map(d => d.name_uz),
                    title: {
                        text: 'Туманлар'
                    }
                },
                yaxis: {
                    title: {
                        text: 'Активлар сони'
                    }
                },
                title: {
                    text: 'Туманлар бўйича активлар сони',
                    align: 'center'
                },
                colors: ['#34a853']
            };

            const districtChart = new ApexCharts(document.querySelector("#districtBarChart"), districtOptions);
            districtChart.render();

            // Chart for building types
            const buildingTypeCounts = @json($buildingTypeCounts);

            const buildingTypeOptions = {
                chart: {
                    type: 'pie',
                    height: 200
                },
                series: Object.values(buildingTypeCounts),
                labels: Object.keys(buildingTypeCounts),
                title: {
                    text: 'Бино турлари бўйича активлар',
                    align: 'center'
                },
                colors: ['#008ffb', '#feb019']
            };

            const buildingTypeChart = new ApexCharts(document.querySelector("#buildingTypeChart"), buildingTypeOptions);
            buildingTypeChart.render();
        });
    </script>

    <!-- core:js -->
    <script src="{{ asset('edo_template/assets/vendors/core/core.js') }}"></script>
    <!-- endinject -->
    <!-- plugin js for this page -->
    <script src="{{ asset('edo_template/assets/vendors/apexcharts/apexcharts.min.js') }}"></script>
    <!-- end plugin js for this page -->
@endsection
