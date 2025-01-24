<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Aktiv;
use App\Models\District;
use App\Models\Street;
use App\Models\SubStreet;
use Maatwebsite\Excel\Facades\Excel;

class AktivSeeder extends Seeder
{
    public function run()
    {
        $filePath = public_path('chastniy_data.xlsx');

        Excel::import(new class implements \Maatwebsite\Excel\Concerns\ToCollection {
            public function collection($rows)
            {
                // Skip the header row
                $rows = $rows->slice(1);

                // Seed districts
                $districts = $rows->pluck(2)->unique();
                foreach ($districts as $districtName) {
                    District::firstOrCreate([
                        'region_id' => 1,
                        'name_uz' => $districtName,
                    ]);
                }

                // Seed streets
                $streets = $rows->map(function ($row) {
                    return [
                        'district_name' => $row[2],
                        'name' => $row[3] // Street is now in column 3
                    ];
                })->unique(function ($item) {
                    return $item['district_name'] . $item['name'];
                });

                foreach ($streets as $streetData) {
                    $district = District::where('name_uz', $streetData['district_name'])->first();
                    Street::firstOrCreate([
                        'district_id' => $district->id,
                        'name' => $streetData['name']
                    ]);
                }

                // Seed sub-streets
                $subStreets = $rows->map(function ($row) {
                    return [
                        'district_name' => $row[2],
                        'name' => $row[4] // SubStreet is now in column 4
                    ];
                })->unique(function ($item) {
                    return $item['district_name'] . $item['name'];
                });

                foreach ($subStreets as $subStreetData) {
                    $district = District::where('name_uz', $subStreetData['district_name'])->first();
                    SubStreet::firstOrCreate([
                        'district_id' => $district->id,
                        'name' => $subStreetData['name']
                    ]);
                }

                // Seed aktivs
                foreach ($rows as $row) {
                    $district = District::where('name_uz', $row[2])->first();
                    $street = Street::where('district_id', $district->id)->where('name', $row[3])->first();
                    $subStreet = SubStreet::where('district_id', $district->id)->where('name', $row[4])->first();

                    $aktivData = [
                        'user_id' => 1, // Assuming a default user ID
                        'action' => 'created', // Change this to a valid value
                        'action_timestamp' => now(),
                        'object_name' => $row[1],
                        'balance_keeper' => $row[6],
                        'location' => "{$row[2]}, {$row[3]}, {$row[4]}, {$row[5]}",
                        'land_area' => $row[8],
                        'building_area' => $row[9],
                        'gas' => 'Бор', // Default value, adjust as needed
                        'water' => 'Бор', // Default value, adjust as needed
                        'electricity' => 'Бор', // Default value, adjust as needed
                        'additional_info' => null, // Default value, adjust as needed
                        'geolokatsiya' => null, // Default value, adjust as needed
                        'latitude' => 0.0, // Default value, adjust as needed
                        'longitude' => 0.0, // Default value, adjust as needed
                        'kadastr_raqami' => $row[10],
                        'street_id' => $street ? $street->id : null,
                        'sub_street_id' => $subStreet ? $subStreet->id : null,
                        'building_type' => null, // Default value, adjust as needed
                        'kadastr_pdf' => null, // Default value, adjust as needed
                        'hokim_qarori_pdf' => null, // Default value, adjust as needed
                        'transfer_basis_pdf' => null, // Default value, adjust as needed
                    ];

                    Aktiv::create($aktivData);
                }
            }
        }, $filePath);
    }
}