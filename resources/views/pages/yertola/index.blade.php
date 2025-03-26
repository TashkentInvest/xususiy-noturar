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
                    <tr>
                        <th>#</th>
                        <th>📍 Манзил</th>
                        <th>🏠 Ер тўла</th>
                        <th>✅ Фойдаланиш</th>
                        <th>⚙ Амаллар</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($yertolas as $yertola)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $yertola->sub_street_id }} / {{ $yertola->street_id }}</td>
                            <td>
                                <span
                                    class="badge text-light {{ $yertola->does_exists_yer_tola ? 'bg-success' : 'bg-danger' }}">
                                    {{ $yertola->does_exists_yer_tola ? 'Мавжуд' : 'Мавжуд эмас' }}
                                </span>
                            </td>
                            <td>
                                <span
                                    class="badge text-light {{ $yertola->does_can_we_use_yer_tola ? 'bg-primary' : 'bg-warning' }}">
                                    {{ $yertola->does_can_we_use_yer_tola ? 'Ҳа' : 'Йўқ' }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#detailsModal{{ $yertola->id }}">
                                    🔍 Маълумот
                                </button>
                                <a href="{{ route('yertola.edit', $yertola->id) }}" class="btn btn-warning btn-sm">✏️
                                    Таҳрирлаш</a>
                                <form action="{{ route('yertola.destroy', $yertola->id) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Ростдан ҳам ўчиришни хоҳлайсизми?')">
                                        🗑️ Ўчириш
                                    </button>
                                </form>
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
                                        <ul class="list-group">
                                            <li class="list-group-item"><strong>📍 Манзил:</strong>
                                                {{ $yertola->sub_street_id }} / {{ $yertola->street_id }}</li>
                                            <li class="list-group-item"><strong>🏠 Ер тўла:</strong>
                                                {{ $yertola->does_exists_yer_tola ? 'Мавжуд' : 'Мавжуд эмас' }}</li>
                                            <li class="list-group-item"><strong>✅ Фойдаланиш мумкин:</strong>
                                                {{ $yertola->does_can_we_use_yer_tola ? 'Ҳа' : 'Йўқ' }}</li>
                                            <li class="list-group-item"><strong>👤 Бошқарувчи:</strong>
                                                {{ $yertola->balance_keeper ?? 'Маълумот йўқ' }}</li>
                                            <li class="list-group-item"><strong>🛠 Стихия:</strong>
                                                {{ $yertola->stir ?? 'Маълумот йўқ' }}</li>
                                            <li class="list-group-item"><strong>📏 Ижарага берилган қисм:</strong>
                                                {{ $yertola->ijaraga_berilgan_qismi_yer_tola ?? 'Маълумот йўқ' }} м²</li>
                                            <li class="list-group-item"><strong>📏 Ижарага берилмаган қисм:</strong>
                                                {{ $yertola->ijaraga_berilmagan_qismi_yer_tola ?? 'Маълумот йўқ' }} м²</li>
                                            <li class="list-group-item"><strong>⚙ Техник қисм:</strong>
                                                {{ $yertola->texnik_qismi_yer_tola ?? 'Маълумот йўқ' }} м²</li>
                                            <li class="list-group-item"><strong>💰 Ойлик ижара нархи:</strong>
                                                {{ number_format($yertola->oylik_ijara_narxi_yer_tola, 0, ',', ' ') ?? 'Маълумот йўқ' }}
                                                сум</li>
                                            <li class="list-group-item"><strong>🏢 Фаолият тури:</strong>
                                                @if ($yertola->faoliyat_turi)
                                                    @foreach (json_decode($yertola->faoliyat_turi, true) as $activity)
                                                        <span
                                                            class="badge bg-secondary text-light">{{ $activity }}</span>
                                                    @endforeach
                                                @else
                                                    Маълумот йўқ
                                                @endif
                                            </li>

                                            <li class="list-group-item"><strong>📆 Яратилган сана:</strong>
                                                {{ $yertola->created_at->format('d.m.Y') }}</li>
                                            <li class="list-group-item"><strong>📆 Охирги таҳрир:</strong>
                                                {{ $yertola->updated_at->format('d.m.Y H:i') }}</li>
                                            {{-- @dd($yertola->latitude)
@dd($yertola->longitude)
<iframe src="{{$yertola->geolokatsiya}}" frameborder="0"></iframe> --}}
                                        </ul>
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
