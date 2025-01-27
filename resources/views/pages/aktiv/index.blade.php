@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h5">Активлар сони: {{ $aktivs->total() ?? '' }}</h2>
        <a href="{{ route('aktivs.create') }}" class="btn btn-secondary btn-sm">
            <i class="btn-icon-prepend" data-feather="plus"></i> Янги актив яратиш
        </a>
    </div>

    <form action="{{ route('aktivs.index') }}" method="get">


        <div class="row my-2">
            <div class="d-flex justify-content-start">
                <label for="kadastr_raqami"></label>
                <input type="text" class="form-control form-control-sm" name="kadastr_raqami"
                    placeholder="Кадастр рақами" id="kadastr_raqami" value="{{ request()->input('kadastr_raqami') }}">
            </div>

            <div class="d-flex justify-content-start">
                <label for="stir"></label>
                <input type="text" class="form-control form-control-sm" name="stir" placeholder="stir" id="stir"
                    value="{{ request()->input('stir') }}">
                <button type="submit" name="filter" class="btn btn-primary">Филтрлаш</button>
            </div>
        </div>

    </form>

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
                        <th scope="col text-bold">STIR</th>
                        <th scope="col text-bold">Объект номи</th>
                        <th scope="col text-bold">Туман</th>
                        <th scope="col text-bold">Мфй</th>
                        <th scope="col text-bold">Кўча</th>
                        <th scope="col text-bold">Уй</th>
                        <th scope="col text-bold">24/7</th>
                        {{-- <th scope="col text-bold">Сана</th> --}}
                        <th scope="col text-bold">Ҳаракатлар</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($aktivs as $aktiv)
                        {{-- @dd($aktiv) --}}
                        <tr>
                            <td class="fw-bold small">{{ $aktiv->id ?? 'No Name' }}

                                {{-- <small class="text-muted">{{ $aktiv->user->email ?? 'No Email' }}</small> --}}
                            </td>
                            <td class="fw-bold small">
                                {{ $aktiv->kadastr_raqami ?? 'No kadastr' }}<br>
                            </td>
                            <td class="fw-bold small">
                                {{ $aktiv->stir ?? 'No Stir' }}<br>
                            </td>
                            <td style="max-width: 400px" class="text-truncate small" title="{{ $aktiv->object_name }}">
                                {{ $aktiv->object_name }}
                            </td>
                            <td class="text-truncate small"
                                title="{{ $aktiv->subStreet->district->name_uz ?? 'Маълумот йўқ' }}">
                                {{ $aktiv->subStreet->district->name_uz ?? 'Маълумот йўқ' }}
                            </td>
                            <td class="text-truncate small"
                                title="{{ $aktiv->street->name ?? 'Маълумот йўқ' }}">
                                {{ $aktiv->street->name ?? 'Маълумот йўқ' }}
                            </td>

                            <td class="text-truncate small"
                                title="{{ $aktiv->subStreet->name ?? 'Маълумот йўқ' }}">
                                {{ $aktiv->subStreet->name ?? 'Маълумот йўқ' }}
                            </td>
                            
                            <td class="text-truncate small"
                                title="{{ $aktiv->home_number ?? 'Маълумот йўқ' }}">
                                {{ $aktiv->home_number ?? 'Маълумот йўқ' }}
                            </td>
                            <td class="small">
                                {{ $aktiv->working_24_7 ? 'Ха' : 'Йўқ' }}
                            </td>
                            {{-- <td class="small">{{ $aktiv->created_at->format('d-m-Y H:i') }}</td> --}}
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
