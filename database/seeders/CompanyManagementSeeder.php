<?php

namespace Database\Seeders;

use App\Models\CompanyManagement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Facades\Excel;

class CompanyManagementSeeder extends Seeder
{
    public function run()
    {
        $importHandler = new class implements ToCollection {
            public function collection(Collection $rows)
            {
                // Skip the first row if it's a header (optional)
                foreach ($rows as $index => $row) {
                    // Optional: Skip first row if it's header
                    if ($index === 0) continue;

                    CompanyManagement::create([
                        'district'       => $row[0] ?? null, // Тошкент шаҳри бўйича жами:
                        'address'        => $row[1] ?? null, // probably address
                        'inn'            => $row[2] ?? null, // ИНН
                        'organization'   => $row[3] ?? null, // Name or company
                        'representative' => $row[4] ?? null, // ФИШ
                        'phone'          => $row[5] ?? null, // Телефон рақам
                        'service_phone'  => $row[6] ?? null, // Ҳизмат телефони
                    ]);
                }
            }
        };

        $filePath = public_path('БСК адрсной.xlsx');

        Excel::import($importHandler, $filePath);

        echo "CompanyManagement records inserted." . PHP_EOL;
    }
}
