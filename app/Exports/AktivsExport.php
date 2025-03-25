<?php

namespace App\Exports;

use App\Models\Aktiv;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AktivsExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        $aktivs = Aktiv::with(['files', 'street.district', 'substreet'])
            ->whereHas('street.district', function ($query) {
                $query->where('name_uz', 'Sergeli');
            })
            ->whereHas('street', function ($query) {
                $query->where('name', 'Nilufar');
            })
            ->get();

        // Map the aktivs to the desired format for export
        return $aktivs->map(function ($aktiv) {
            return [
                'object_name' => $aktiv->object_name ?? '',
                'building_type' => $aktiv->building_type ?? '',
                'balance_keeper' => $aktiv->balance_keeper ?? '',
                'location' => $aktiv->location ?? '',
                'land_area' => $aktiv->land_area ?? '',
                'building_area' => $aktiv->building_area ?? '',
                'gas' => $aktiv->gas ?? '',
                'water' => $aktiv->water ?? '',
                'electricity' => $aktiv->electricity ?? '',
                'additional_info' => $aktiv->additional_info ?? '',
                'geolokatsiya' => $aktiv->geolokatsiya ?? '',
                'latitude' => $aktiv->latitude ?? '',
                'longitude' => $aktiv->longitude ?? '',
                'kadastr_raqami' => $aktiv->kadastr_raqami ?? '',
                'user_id' => $aktiv->user->email ?? '',
                'district_name' => $aktiv->street->district->name_uz ?? '', // District name
                'street_id' => $aktiv->street->name ?? '', // Street name
                'sub_street_id' => $aktiv->substreet->name ?? '', // Substreet name
                'kadastr_pdf_exists' => $aktiv->kadastr_pdf ? 1 : 0,
                // 'ijara_shartnoma_nusxasi_pdf_exists' => $aktiv->ijara_shartnoma_nusxasi_pdf ? 1 : 0,
                // 'qoshimcha_fayllar_pdf_exists' => $aktiv->qoshimcha_fayllar_pdf ? 1 : 0,
                'id' => "https://aktiv.toshkentinvest.uz/aktivs/" . $aktiv->id,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Объект номи',
            'Бино тури',
            'Баланс сақловчи',
            'Жойлашуви',
            'Ер майдони',
            'Бино майдони',
            'Газ',
            'Сув',
            'Электр',
            'Қўшимча маълумот',
            'Геолокация',
            'Кенглик',
            'Узунлик',
            'Кадастр рақами',
            'Фойдаланувчи ИД',
            'Туман номи',
            'МФЙ номи',
            'Кўча номи',
            'Кадастр ПДФ мавжудлиги',
            // 'Ҳоким қарори ПДФ мавжудлиги',
            // 'Трансфер асоси ПДФ мавжудлиги',
            'ИД',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Styling the first row as bold text.
            1 => ['font' => ['bold' => true]],
        ];
    }
}
