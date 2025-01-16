<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Aktiv;
use Maatwebsite\Excel\Facades\Excel;

class AktivSeeder extends Seeder
{
    public function run()
    {
        // Path to your Excel file
        $filePath = public_path('chastniy_data.xlsx');

        // Load the data from the Excel file
        Excel::import(new class implements \Maatwebsite\Excel\Concerns\ToCollection {
            public function collection($rows)
            {
                foreach ($rows as $index => $row) {
                    // Skip the header row
                    if ($index === 0) {
                        continue;
                    }

                    // Extract data from each row
                    $aktivData = [
                        'user_id' => 1, // Assuming a default user ID
                        'action' => 'created', // Change this to a valid value
                        'action_timestamp' => now(),
                        'object_name' => $row[6],
                        'balance_keeper' => $row[1],
                        'location' => "{$row[2]}, {$row[3]}, {$row[4]}, {$row[5]}",
                        'land_area' => $row[8],
                        'building_area' => $row[9],
                        'gas' => 'yes', // Default value, adjust as needed
                        'water' => 'yes', // Default value, adjust as needed
                        'electricity' => 'yes', // Default value, adjust as needed
                        'additional_info' => null, // Default value, adjust as needed
                        'geolokatsiya' => null, // Default value, adjust as needed
                        'latitude' => 0.0, // Default value, adjust as needed
                        'longitude' => 0.0, // Default value, adjust as needed
                        'kadastr_raqami' => $row[10],
                        'sub_street_id' => null, // Default value, adjust as needed
                        'street_id' => null, // Default value, adjust as needed
                        'building_type' => null, // Default value, adjust as needed
                        'kadastr_pdf' => null, // Default value, adjust as needed
                        'hokim_qarori_pdf' => null, // Default value, adjust as needed
                        'transfer_basis_pdf' => null, // Default value, adjust as needed
                    ];

                    // Insert the data into the database
                    Aktiv::create($aktivData);
                }
            }
        }, $filePath);
    }
}