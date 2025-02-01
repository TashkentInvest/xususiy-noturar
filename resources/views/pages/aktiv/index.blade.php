@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h6 text-muted">Активлар сони: <span class="text-primary">{{ $aktivs->total() ?? '' }}</span></h2>
        <a href="{{ route('aktivs.create') }}" class="btn btn-primary btn-sm d-flex align-items-center">
            <i class="btn-icon-prepend" data-feather="plus" style="margin-right: 4px;"></i> Янги актив яратиш
        </a>
    </div>

    <form action="{{ route('aktivs.index') }}" method="get" class="mb-3">
        <div class="row g-2">
            <div class="col-md-3 col-6">
                <input type="text" class="form-control form-control-sm" name="kadastr_raqami"
                    placeholder="Кадастр рақами" id="kadastr_raqami" value="{{ request()->input('kadastr_raqami') }}">
            </div>
            <div class="col-md-3 col-6">
                <input type="text" class="form-control form-control-sm" name="stir" placeholder="Стир" id="stir"
                    value="{{ request()->input('stir') }}">
            </div>

            <div class="col-md-3 col-6">
                <select class="form-control form-control-sm district_id select2" name="district_id" id="district_id">
                    <option value="">Туман</option>
                    @foreach ($districts as $district)
                        <option value="{{ $district->id }}"
                            {{ request()->input('district_id') == $district->id ? 'selected' : '' }}>
                            {{ $district->name_uz }}
                        </option>
                    @endforeach
                </select>
                
                <span class="text-danger error-message" id="district_id_error"></span>
            </div>

            <div class="col-md-3 col-6">
                    <div class="d-flex align-items-end">
                        <select class="form-control select2 street_id" name="street_id" id="street_id">
                            <option value="">Мфй ни танланг</option>
                        </select>
                    </div>
                    <span class="text-danger error-message" id="street_id_error"></span>
            </div>
            <div class="col-md-2 col-6">
                <button type="submit" name="filter" class="btn btn-primary btn-sm w-100">Филтрлаш</button>
            </div>
        </div>


        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/js/select2.min.js"></script>
        <script>
            $(document).ready(function() {
                // Initialize select2
                $('.select2').select2();

                // When a district is selected
                $('#district_id').change(function() {
                    var districtId = $(this).val();
                    if (districtId) {
                        $.ajax({
                            url: "{{ route('get.Obstreets') }}",
                            type: "GET",
                            data: {
                                district_id: districtId
                            },
                            success: function(data) {
                                $('#street_id').empty().append(
                                    '<option value="">Мфй ни танланг</option>');
                                $.each(data, function(key, value) {
                                    $('#street_id').append('<option value="' + key + '">' +
                                        value + '</option>');
                                });

                                // Update sub-streets for the selected district
                                $.ajax({
                                    url: "{{ route('get.Obsubstreets') }}",
                                    type: "GET",
                                    data: {
                                        district_id: districtId
                                    },
                                    success: function(substreets) {
                                        $('#sub_street_id').empty().append(
                                            '<option value="">Кўчани танланг</option>'
                                        );
                                        $.each(substreets, function(key, value) {
                                            $('#sub_street_id').append(
                                                '<option value="' + key +
                                                '">' + value + '</option>');
                                        });
                                    },
                                    error: function(xhr, status, error) {
                                        console.error('Error fetching sub-streets:',
                                            error);
                                        $('#sub_street_id').empty().append(
                                            '<option value="">Кўчани танланг</option>'
                                        );
                                    }
                                });
                            },
                            error: function(xhr, status, error) {
                                console.error('Error fetching streets:', error);
                                $('#street_id').empty().append(
                                    '<option value="">Мфй ни танланг</option>');
                                $('#sub_street_id').empty().append(
                                    '<option value="">Кўчани танланг</option>');
                            }
                        });
                    } else {
                        $('#street_id').empty().append('<option value="">Мфй ни танланг</option>');
                        $('#sub_street_id').empty().append('<option value="">Кўчани танланг</option>');
                    }
                });

             
            });
        </script> 

    </form>

    <style>
        table.table {
            font-size: 10px;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 4px !important;
            text-align: center;
            vertical-align: middle;
        }

        .table thead th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .table-hover tbody tr:hover {
            background-color: #f1f3f5;
        }

        .btn-icon-prepend {
            font-size: 10px;
        }

        .cusom_icon {
            width: 14px;
            height: 14px;
        }

        .alert {
            font-size: 10px;
        }
    </style>

    @if ($aktivs->count())
        <div class="table-responsive">
            <table class="table table-hover table-bordered table-sm mb-0">
                <thead>
                    <tr>
                        <th>№</th>
                        <th>Стир</th>
                        <th>Компания</th>
                        <th>Объект номи</th>
                        <th>Туман</th>
                        <th>МФЙ</th>
                        <th>Кўча</th>
                        <th>Уй</th>
                        <th>24/7</th>
                        <th>Кадастр рақами</th>
                        <th>Ҳаракатлар</th>
                    </tr>
                </thead>
                {{-- <pre>{{ print_r($aktivs->toArray(), true) }}</pre> --}}
                <tbody>
                    @foreach ($aktivs as $aktiv)
                        <tr>
                            <td>{{ $aktiv->id ?? 'Маълумот йўқ' }}</td>
                            <td>{{ $aktiv->stir ?? 'Маълумот йўқ' }}</td>
                            <td style="max-width: 150px; white-space: pre-wrap; word-wrap: break-word; text-align: start;">
                                @if ($aktiv->balance_keeper)
                                    {!! nl2br(e($aktiv->balance_keeper)) !!}
                                @else
                                    Маълумот йўқ
                                @endif
                            </td>

                            <td class="text-truncate" style="max-width: 150px;" title="{{ $aktiv->object_name }}">
                                {{ $aktiv->object_name }}
                            </td>
                            <td>{{ $aktiv->subStreet->district->name_uz ?? 'Маълумот йўқ' }}</td>
                            <td>{{ $aktiv->street->name ?? 'Маълумот йўқ' }}</td>
                            <td>{{ $aktiv->subStreet->name ?? 'Маълумот йўқ' }}</td>
                            <td>{{ $aktiv->home_number ?? 'Маълумот йўқ' }}</td>
                            <td>{{ $aktiv->working_24_7 ? 'Ха' : 'Йўқ' }}</td>
                            <td>{{ $aktiv->kadastr_raqami ?? 'Маълумот йўқ' }}</td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('aktivs.show', $aktiv) }}" class="btn btn-outline-secondary btn-sm"
                                        title="Кўриш">
                                        <i class="btn-icon-prepend cusom_icon" data-feather="eye"></i>
                                    </a>
                                    <a href="{{ route('aktivs.edit', $aktiv) }}" class="btn btn-outline-secondary btn-sm"
                                        title="Таҳрирлаш">
                                        <i class="btn-icon-prepend cusom_icon" data-feather="edit"></i>
                                    </a>
                                    @if (auth()->user()->roles->first()->name == 'Super Admin')
                                        <form action="{{ route('aktivs.destroy', $aktiv) }}" method="POST"
                                            class="d-inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Ўчириш"
                                                onclick="return confirm('Сиз ростдан ҳам бу объектни ўчиришни истайсизми?');">
                                                <i class="btn-icon-prepend cusom_icon" data-feather="trash-2"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination Controls -->
        <div class="d-flex justify-content-center mt-3">
            {{ $aktivs->links('pagination::bootstrap-4') }}
        </div>
    @else
        <div class="alert alert-warning text-center mt-3">
            <i class="btn-icon-prepend" data-feather="alert-circle"></i> Активлар топилмади.
        </div>
    @endif
@endsection
