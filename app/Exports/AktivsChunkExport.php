<?php

namespace App\Exports;

use App\Models\Aktiv;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomChunkSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Contracts\Queue\ShouldQueue;

class AktivsChunkExport implements FromQuery, WithHeadings, WithMapping, WithCustomChunkSize, WithEvents, ShouldAutoSize, ShouldQueue
{
    protected $user;
    protected $isYerTola;
    protected $districtStats = [];

    public function __construct($user, $isYerTola = false)
    {
        $this->user = $user;
        $this->isYerTola = $isYerTola;
    }

    public function query()
    {
        $userRole = $this->user->roles[0]->name ?? '';

        $query = Aktiv::query()
            ->with(['street.district', 'user', 'files'])
            ->where('is_status_yer_tola', $this->isYerTola ? 1 : '!=', 1);

        if ($userRole === 'Manager') {
            // Managers see only aktivs in their district
            $query->whereHas('street.district', function ($q) {
                $q->where('id', $this->user->district_id);
            });
        }

        return $query;
    }

    // All other methods remain the same as in AktivsExport
    // ...
}
