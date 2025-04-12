<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\District;
use Spatie\Permission\Models\Role;

class DistrictManagerSeeder extends Seeder
{
    public function run()
    {
        $districtMap = [
            'Uchtepa',
            'Bektemir',
            'Chilonzor',
            'Yashnobod',
            'Yakkasaroy',
            'Sergeli',
            'Yunusobod',
            'Olmazor',
            'Mirzo Ulug‘bek',
            'Shayxontohur',
            'Mirobod',
            'Yangihayot'
        ];

        $managerRole = Role::where('name', 'Manager')->first();

        if (!$managerRole) {
            $this->command->error("⚠️ Role 'Manager' not found! Run RoleSeeder first.");
            return;
        }

        foreach ($districtMap as $districtName) {
            $district = District::where('name_uz', $districtName)->first();

            if (!$district) {
                $this->command->warn("❌ District '$districtName' not found in DB.");
                continue;
            }

            // Clean district name for email
            $emailPrefix = strtolower(str_replace([' ', '‘', '’', 'ʼ', 'ʻ', '\''], '', $districtName));
            $email = $emailPrefix . 'manager@hokimligi.uz';

            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $districtName . ' Manager',
                    'password' => Hash::make('hokimyat'),
                    'district_id' => $district->id,
                    'email_verified_at' => now(),
                ]
            );

            if (!$user->hasRole('Manager')) {
                $user->assignRole($managerRole);
            }

            $this->command->info("✅ Manager created: {$user->email} (District ID: {$district->id})");
        }
    }
}
