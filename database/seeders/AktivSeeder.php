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

        // Define the mapping for district names to their normalized forms
        $districtMapping = [
            'Uchtepa' => 'Uchtepa',
            'Учтепинский' => 'Uchtepa',
            'Bektemir' => 'Bektemir',
            'Бектемирский' => 'Bektemir',
            'Chilonzor' => 'Chilonzor',
            'Чиланзарский' => 'Chilonzor',
            'Yashnobod' => 'Yashnobod',
            'Яшнабадский' => 'Yashnobod',
            'Yakkasaroy' => 'Yakkasaroy',
            'Яккасарайский' => 'Yakkasaroy',
            'Sergeli' => 'Sergeli',
            'Сергелийский' => 'Sergeli',
            'Yunusobod' => 'Yunusobod',
            'Юнусабадский' => 'Yunusobod',
            'Olmazor' => 'Olmazor',
            'Олмазарский' => 'Olmazor',
            'Mirzo Ulug‘bek' => 'Mirzo Ulug‘bek',
            'Мирзо Улугбекский' => 'Mirzo Ulug‘bek',
            'Shayxontohur' => 'Shayxontohur',
            'Шайхантахурский' => 'Shayxontohur',
            'Mirobod' => 'Mirobod',
            'Мирабадский' => 'Mirobod',
            'Yangihayot' => 'Yangihayot',
            'Янгихаётский' => 'Yangihayot',
            'Bektemir tumani' => 'Bektemir',
            'Chilonzor tumani' => 'Chilonzor',
            'Mirobod tumani' => 'Mirobod',
            'Mirzo ulug‘bek tumani' => 'Mirzo Ulug‘bek',
            'Olmazor tumani' => 'Olmazor',
            'Shayxontohur tumani' => 'Shayxontohur',
            'Sirg‘ali tumani' => 'Sergeli',
            'Uchtepa tumani' => 'Uchtepa',
            'Yakkasaroy tumani' => 'Yakkasaroy',
            'Yangihayot tumani' => 'Yangihayot',
            'Yashnobod tumani' => 'Yashnobod',
            'Yunusobod tumani' => 'Yunusobod',
        ];

        Excel::import(new class($districtMapping) implements \Maatwebsite\Excel\Concerns\ToCollection {
            private $districtMapping;

            public function __construct($districtMapping)
            {
                $this->districtMapping = $districtMapping;
            }

            public function collection($rows)
            {
                // Skip the header row
                $rows = $rows->slice(1);

                // Seed districts
                $normalizedDistricts = $rows->pluck(2)->map(function ($districtName) {
                    return $this->districtMapping[$districtName] ?? $districtName;
                })->unique();

                foreach ($normalizedDistricts as $districtName) {
                    District::firstOrCreate([
                        'region_id' => 1,
                        'name_uz' => $districtName,
                    ]);
                }

                // Seed streets
                $streets = $rows->map(function ($row) {
                    return [
                        'district_name' => $this->districtMapping[$row[2]] ?? $row[2],
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
                        'district_name' => $this->districtMapping[$row[2]] ?? $row[2],
                        'street_name' => $row[3],
                        'name' => $row[4] // SubStreet is now in column 4
                    ];
                })->unique(function ($item) {
                    return $item['district_name'] . $item['street_name'] . $item['name'];
                });

                foreach ($subStreets as $subStreetData) {
                    $district = District::where('name_uz', $subStreetData['district_name'])->first();
                    $street = Street::where('district_id', $district->id)->where('name', $subStreetData['street_name'])->first();

                    SubStreet::firstOrCreate([
                        'district_id' => $district->id,
                        'street_id' => $street->id,
                        'name' => $subStreetData['name'],
                        'name_ru' => null,
                        'type' => null,
                        'code' => null,
                        'comment' => null,
                    ]);
                }

                // Seed aktivs
                foreach ($rows as $row) {
                    $districtName = $this->districtMapping[$row[2]] ?? $row[2];
                    $district = District::where('name_uz', $districtName)->first();
                    $street = Street::where('district_id', $district->id)->where('name', $row[3])->first();
                    $subStreet = SubStreet::where('district_id', $district->id)->where('name', $row[4])->first();

                    $aktivData = [
                        'user_id' => 1, // Assuming a default user ID
                        'action' => 'created', // Change this to a valid value
                        'action_timestamp' => now(),
                        'object_name' => $row[1],
                        'balance_keeper' => $row[6],
                        'location' => "{$districtName}, {$row[3]}, {$row[4]}, {$row[5]}",
                        'stir' => $row[7],
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
                        'home_number' => "{$row[5]}", // Default value, adjust as needed
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
