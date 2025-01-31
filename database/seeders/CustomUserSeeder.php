<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Street;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CustomUserSeeder extends Seeder
{
    public function run()
    {
        $startTime = Carbon::now();
        Log::info('User seeding started at: ' . $startTime);

        $streets = Street::all();

        foreach ($streets as $street) {
            $login = str_replace(' ', '_', "{$street->name}@hokimligi.uz");
            $password = 'secret'; // Use a better password management approach in production

            $globalAddressData = [
                'region_id' => 1,  // Assuming a default region ID
                'district_id' => $street->district_id,
                'street_id' => $street->id,
                'sub_street_id' => null, // Placeholder for sub-street ID if needed
                'home_number' => null, // Placeholder for home number if needed
            ];

            $user = User::firstOrCreate([
                'email' => $login
            ], [
                'name' => $street->name ?? 'Unknown',
                'password' => Hash::make($password),
                'district_id' => $street->district_id,
                'global_address_id' => json_encode($globalAddressData),
                'theme' => 'default'
            ]);

            // Update street with the user ID
            $street->update(['user_id' => $user->id]);
        }

        $endTime = Carbon::now();
        Log::info('User seeding completed at: ' . $endTime);

        $duration = $endTime->diffInSeconds($startTime);
        Log::info('User seeding duration: ' . $duration . ' seconds');
    }
}