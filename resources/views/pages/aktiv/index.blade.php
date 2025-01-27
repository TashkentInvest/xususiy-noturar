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
            <div class="col-md-2 col-6">
                <button type="submit" name="filter" class="btn btn-primary btn-sm w-100">Филтрлаш</button>
            </div>
        </div>
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
                            <td>{{ $aktiv->subStreet->name ?? 'Маълумот йўқ' }}</td>
                            <td>{{ $aktiv->street->name ?? 'Маълумот йўқ' }}</td>
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
                                    @if (auth()->user()->roles[0]->name == 'Manager')
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
