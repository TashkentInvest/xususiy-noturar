<?php

namespace App\Exports;

use App\Models\Aktiv;
use App\Models\District;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomChunkSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Contracts\Queue\ShouldQueue;

class AktivsExport implements FromCollection, WithHeadings, WithMapping, WithCustomChunkSize, WithEvents, ShouldAutoSize, WithTitle
{
    protected $user;
    protected $isYerTola;
    protected $districtStats = [];

    public function __construct($user, $isYerTola = false)
    {
        $this->user = $user;
        $this->isYerTola = $isYerTola;
    }

    public function collection()
    {
        $userRole = $this->user->roles[0]->name ?? '';

        $query = Aktiv::with(['street.district', 'user', 'files'])
            ->where('is_status_yer_tola', $this->isYerTola ? 1 : '!=', 1);

        if ($userRole === 'Manager') {
            // Managers see only aktivs in their district
            $query->whereHas('street.district', function ($q) {
                $q->where('id', $this->user->district_id);
            });

            // Get stats only for this district
            $this->districtStats = $this->getDistrictStatistics([$this->user->district_id]);
        } elseif ($userRole === 'Super Admin') {
            // Get stats for all districts
            $districtIds = District::pluck('id')->toArray();
            $this->districtStats = $this->getDistrictStatistics($districtIds);
        }

        return $query->get();
    }

    /**
     * Get district statistics
     */
    protected function getDistrictStatistics($districtIds)
    {
        $stats = [];

        // Get districts with counts
        $districtsWithCounts = DB::table('aktivs')
            ->join('streets', 'aktivs.street_id', '=', 'streets.id')
            ->join('districts', 'streets.district_id', '=', 'districts.id')
            ->whereIn('districts.id', $districtIds)
            ->where('aktivs.is_status_yer_tola', $this->isYerTola ? 1 : '!=', 1)
            ->select(
                'districts.id',
                'districts.name_uz',
                DB::raw('COUNT(aktivs.id) as total_aktivs'),
                DB::raw('COUNT(CASE WHEN aktivs.building_type = "AlohidaSavdoDokoni" THEN 1 END) as noturar_bino_count'),
                DB::raw('COUNT(CASE WHEN aktivs.building_type = "kopQavatliUy" THEN 1 END) as turar_bino_count'),
                DB::raw('COUNT(CASE WHEN aktivs.building_type = "yer" THEN 1 END) as yer_count')
            )
            ->groupBy('districts.id', 'districts.name_uz')
            ->get();

        foreach ($districtsWithCounts as $district) {
            $stats[] = [
                'district_id' => $district->id,
                'district_name' => $district->name_uz,
                'total_aktivs' => $district->total_aktivs,
                'noturar_bino_count' => $district->noturar_bino_count,
                'turar_bino_count' => $district->turar_bino_count,
                'yer_count' => $district->yer_count
            ];
        }

        return $stats;
    }

    /**
     * Convert Aktiv model to an array for the Excel export
     */
    public function map($aktiv): array
    {
        // Convert data to Uzbek Cyrillic where needed
        return [
            'ID' => $aktiv->id,
            'Объект номи' => $aktiv->object_name,
            'Объект тури' => $aktiv->object_type,
            'Балансда сақловчи' => $aktiv->balance_keeper,
            'Жойлашган ҳудуди' => $aktiv->street->district->name ?? '',
            'Кўча номи' => $aktiv->street->name ?? '',
            'Манзил' => $aktiv->location,
            'Ер майдони' => $aktiv->land_area,
            'Бино майдони' => $aktiv->building_area,
            'Газ' => $aktiv->gas,
            'Сув' => $aktiv->water,
            'Электр' => $aktiv->electricity,
            'Қўшимча маълумот' => $aktiv->additional_info,
            'Кадастр рақами' => $aktiv->kadastr_raqami,
            'Бино тури' => $this->getBuildingTypeInUzbek($aktiv->building_type),
            'Ҳужжат тури' => $aktiv->document_type,
            'Фаолият юритмаётганлиги сабаби' => $aktiv->reason_not_active,
            'Ижарага беришга тайёрлиги' => $this->getReadyForRentInUzbek($aktiv->ijaraga_berishga_tayyorligi),
            'Фаолият ҳолати' => $this->getActivityStatusInUzbek($aktiv->faoliyat_xolati),
            'Фаолият юритишни бошлаган сана' => $aktiv->start_date,
            'СТИР' => $aktiv->stir,
            'Телефон рақам' => $aktiv->tenant_phone_number,
            'Ижара суммаси (тахминий)' => $aktiv->ijara_summa_wanted,
            'Ижара суммаси (ҳақиқий)' => $aktiv->ijara_summa_fakt,
            'Яратилган вақти' => $aktiv->created_at,
            'Охирги янгиланиш' => $aktiv->updated_at,
        ];
    }

    /**
     * Convert building type to Uzbek Cyrillic
     */
    protected function getBuildingTypeInUzbek($type)
    {
        return match ($type) {
            'yer' => 'Ер',
            'kopQavatliUy' => 'Кўп қаватли уй',
            'AlohidaSavdoDokoni' => 'Алоҳида савдо дўкони',
            default => $type
        };
    }

    /**
     * Convert ready for rent to Uzbek Cyrillic
     */
    protected function getReadyForRentInUzbek($status)
    {
        return match ($status) {
            'yeap' => 'Ҳа',
            'not' => 'Йўқ',
            default => $status
        };
    }

    /**
     * Convert activity status to Uzbek Cyrillic
     */
    protected function getActivityStatusInUzbek($status)
    {
        return match ($status) {
            'work' => 'Фаолият юритмоқда',
            'notwork' => 'Фаолият юритмаяпти',
            default => $status
        };
    }

    /**
     * Sheet headings
     */
    public function headings(): array
    {
        return [
            'ID',
            'Объект номи',
            'Объект тури',
            'Балансда сақловчи',
            'Жойлашган ҳудуди',
            'Кўча номи',
            'Манзил',
            'Ер майдони (м²)',
            'Бино майдони (м²)',
            'Газ',
            'Сув',
            'Электр',
            'Қўшимча маълумот',
            'Кадастр рақами',
            'Бино тури',
            'Ҳужжат тури',
            'Фаолият юритмаётганлиги сабаби',
            'Ижарага беришга тайёрлиги',
            'Фаолият ҳолати',
            'Фаолият юритишни бошлаган сана',
            'СТИР',
            'Телефон рақам',
            'Ижара суммаси (тахминий)',
            'Ижара суммаси (ҳақиқий)',
            'Яратилган вақти',
            'Охирги янгиланиш',
        ];
    }

    /**
     * Set chunk size for memory optimization
     */
    public function chunkSize(): int
    {
        return 1000; // Process 1000 records at a time
    }

    /**
     * Add statistics to the top of the sheet
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Get the sheet
                $sheet = $event->sheet->getDelegate();

                // If there are district statistics
                if (!empty($this->districtStats)) {
                    // Add title for statistics section
                    $sheet->insertNewRowBefore(1, 3 + count($this->districtStats));
                    $sheet->setCellValue('A1', 'СТАТИСТИКА БЎЙИЧА МАЪЛУМОТ');
                    $sheet->mergeCells('A1:F1');

                    // Set headers for statistics table
                    $sheet->setCellValue('A2', 'Туман номи');
                    $sheet->setCellValue('B2', 'Жами объектлар');
                    $sheet->setCellValue('C2', 'Нотурар бинолар');
                    $sheet->setCellValue('D2', 'Турар жой бинолари');
                    $sheet->setCellValue('E2', 'Ер майдонлари');

                    // Add data for each district
                    $row = 3;
                    foreach ($this->districtStats as $stat) {
                        $sheet->setCellValue('A' . $row, $stat['district_name']);
                        $sheet->setCellValue('B' . $row, $stat['total_aktivs']);
                        $sheet->setCellValue('C' . $row, $stat['noturar_bino_count']);
                        $sheet->setCellValue('D' . $row, $stat['turar_bino_count']);
                        $sheet->setCellValue('E' . $row, $stat['yer_count']);
                        $row++;
                    }

                    // Add empty row to separate statistics from data
                    $sheet->setCellValue('A' . $row, '');

                    // Style the statistics headers
                    $headerRange = 'A1:F2';
                    $sheet->getStyle($headerRange)->getFont()->setBold(true);
                    $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                    // Data section header starts at row + 1
                    $dataHeaderRow = $row + 1;
                    $sheet->getStyle('A' . $dataHeaderRow . ':' . $sheet->getHighestColumn() . $dataHeaderRow)->getFont()->setBold(true);
                }
            },
        ];
    }

    /**
     * Set the sheet title
     */
    public function title(): string
    {
        return $this->isYerTola ? 'Ер тўла объектлар рўйхати' : 'Объектлар рўйхати';
    }
}
