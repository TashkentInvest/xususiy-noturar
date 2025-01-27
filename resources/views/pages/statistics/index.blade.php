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
                            <div class="card text-center shadow-sm p-3 mb-4">
                                <div class="card-body">
                                    <i class="text-primary mb-2" data-feather="database" style="width: 32px; height: 32px;"></i>
                                    <h6 class="text-muted">Жами активлар сони</h6>
                                    <h3 class="font-weight-bold">{{ $totalAktivs }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-center shadow-sm p-3 mb-4">
                                <div class="card-body">
                                    <i class="text-success mb-2" data-feather="clock" style="width: 32px; height: 32px;"></i>
                                    <h6 class="text-muted">24/7 ишлайдиган активлар</h6>
                                    <h3 class="font-weight-bold">{{ $working247Aktivs }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card shadow-sm p-3 mb-4">
                                <div class="card-body">
                                    <i class="text-warning m-auto d-flex justify-content-center" data-feather="home" style="width: 32px; height: 32px;"></i>
                                    <ul class="list-unstyled mt-1">
                                        <li class="d-flex justify-content-between align-items-center">
                                            <span>Кўп қаватли уйлар:</span>
                                            <span class="font-weight-bold">{{ $buildingTypeCounts['kopQavatliUy'] ?? 0 }}</span>
                                        </li>
                                        <li class="d-flex justify-content-between align-items-center">
                                            <span>Алоҳида савдо дўконлари:</span>
                                            <span class="font-weight-bold">{{ $buildingTypeCounts['AlohidaSavdoDokoni'] ?? 0 }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
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
           
        });
    </script>

    <!-- core:js -->
    <script src="{{ asset('edo_template/assets/vendors/core/core.js') }}"></script>
    <!-- endinject -->
    <!-- plugin js for this page -->
    <script src="{{ asset('edo_template/assets/vendors/apexcharts/apexcharts.min.js') }}"></script>
    <!-- end plugin js for this page -->
@endsection
