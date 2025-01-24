@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h5">Активлар сони: {{ $aktivs->total() ?? '' }}</h2>
        <a href="{{ route('aktivs.create') }}" class="btn btn-secondary btn-sm">
            <i class="btn-icon-prepend" data-feather="plus"></i> Янги актив яратиш
        </a>
    </div>

    <style>
        .cusom_icon {
            width: 18px !important;
            font-size: 8px !important;
        }
    </style>

    @if ($aktivs->count())
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
                <thead>
                    <tr>
                        <th scope="col text-bold">№</th>
                        <th scope="col text-bold">Кадастр рақами</th>
                        <th scope="col text-bold">Объект номи</th>
                        <th scope="col text-bold">Мўлжал</th>
                        <th scope="col text-bold">24/7</th>
                        <th scope="col text-bold">Сана</th>
                        <th scope="col text-bold">Ҳаракатлар</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($aktivs as $aktiv)
                        <tr>
                            <td class="fw-bold small">{{ $aktiv->id ?? 'No Name' }}</td>
                            <td class="fw-bold small">
                                {{ $aktiv->kadastr_raqami ?? 'No Name' }}<br>
                                <small class="text-muted">{{ $aktiv->user->email ?? 'No Email' }}</small>
                            </td>
                            <td style="max-width: 400px" class="text-truncate small" title="{{ $aktiv->object_name }}">
                                {{ $aktiv->object_name }}
                            </td>
                            <td style="max-width: 300px" class="text-truncate small"
                                title="{{ $aktiv->location ?? 'Маълумот йўқ' }}">
                                {{ $aktiv->location ?? 'Маълумот йўқ' }}
                            </td>
                            <td class="small">
                                {{ $aktiv->working_24_7 ? 'Ха' : 'Йўқ' }}
                            </td>
                            <td class="small">{{ $aktiv->created_at->format('d-m-Y H:i') }}</td>
                            <td class="text-center">
                                <div class="mx-2">
                                    <a href="{{ route('aktivs.show', $aktiv) }}" class="btn btn-secondary btn-sm"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Кўриш">
                                        <i class="btn-icon-prepend cusom_icon" data-feather="eye"></i>
                                    </a>
                                    <a href="{{ route('aktivs.edit', $aktiv) }}" class="btn btn-secondary btn-sm"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Таҳрирлаш">
                                        <i class="btn-icon-prepend cusom_icon" data-feather="edit"></i>
                                    </a>
                                    @if (auth()->user()->roles[0]->name == 'Manager')
                                        <form action="{{ route('aktivs.destroy', $aktiv) }}" method="POST"
                                            style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="Ўчириш"
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
        <div class="d-flex justify-content-center mt-4">
            {{ $aktivs->links('pagination::bootstrap-4') }}
        </div>
    @else
        <div class="alert alert-warning text-center mt-4">
            <i class="btn-icon-prepend" data-feather="alert-circle"></i> Активлар топилмади.
        </div>
    @endif
@endsection
