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
                    <h6 class="card-title">Туманлар бўйича активлар сони</h6>
                    <div id="districtBarChart"></div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const districts = @json($districts);

                const options = {
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

                const chart = new ApexCharts(document.querySelector("#districtBarChart"), options);
                chart.render();
            });
        </script>

    </div>

    <!-- core:js -->
    <script src="{{ asset('edo_template/assets/vendors/core/core.js') }}"></script>
    <!-- endinject -->
    <!-- plugin js for this page -->
    <script src="{{ asset('edo_template/assets/vendors/apexcharts/apexcharts.min.js') }}"></script>
    <!-- end plugin js for this page -->
    <!-- custom js for this page -->
    <script src="{{ asset('edo_template/assets/js/apexcharts.js') }}"></script>
@endsection
