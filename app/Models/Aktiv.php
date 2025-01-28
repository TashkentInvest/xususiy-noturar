<?php

namespace App\Models;

use App\Services\HistoryService;
use GuzzleHttp\Psr7\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Aktiv extends Model
{
    use HasFactory, SoftDeletes;

    public static function deepFilters()
    {
        $query = self::query();
        $request = request();

        try {
            // Generic filters for fillable attributes
            foreach ((new self())->fillable as $item) {
                if ($request->filled($item)) {
                    $operator = $request->input($item . '_operator', 'like');
                    $value = $request->input($item);

                    if ($operator === 'like') {
                        $value = "%{$value}%";
                    }

                    $query->where($item, $operator, $value);
                }
            }

            // Custom filter for district_id
            if ($request->filled('district_id')) {
                $query->whereHas('street.district', function ($q) use ($request) {
                    $q->where('districts.id', $request->input('district_id'));
                });
            }
        } catch (\Exception $e) {
            \Log::error('Error in deepFilters:', ['error' => $e->getMessage()]);
        }

        return $query;
    }

    // public static function deepFilters()
    // {
    //     $query = self::query();
    //     $request = request();

    //     try {
    //         // Generic filters for fillable attributes
    //         foreach ((new self())->fillable as $item) {
    //             if ($request->filled($item)) {
    //                 $operator = $request->input($item . '_operator', 'like');
    //                 $value = $request->input($item);

    //                 if ($operator === 'like') {
    //                     $value = "%{$value}%";
    //                 }

    //                 $query->where($item, $operator, $value);
    //             }
    //         }

    //         if ($request->filled('district_id')) {
    //             // dd('sad');
    //             $query->whereHas('street.district', function ($q) use ($request) {
    //                 // dd($request);
    //                 $q->where('districts.id', $request->input('district_id'));
    //                 // dd($q);
    //             });
    //         }

    //         // // Address-based filtering
    //         // if ($request->filled('region_id')) {
    //         //     $query->whereHas('substreet.district.region', function ($q) use ($request) {
    //         //         $q->where('regions.id', $request->input('region_id'));
    //         //     });
    //         // }

    //         // if ($request->filled('district_id')) {
    //         //     $query->whereHas('substreet.district', function ($q) use ($request) {
    //         //         $q->where('districts.id', $request->input('district_id'));
    //         //     });
    //         // }

    //         // if ($request->filled('street_id')) {
    //         //     $query->whereHas('substreet.street', function ($q) use ($request) {
    //         //         $q->where('streets.id', $request->input('street_id'));
    //         //     });
    //         // }

    //         // if ($request->filled('sub_street_id')) {
    //         //     $query->where('sub_street_id', $request->input('sub_street_id'));
    //         // }

    //         // // Debugging the query
    //         // \Log::debug('SQL Query:', [
    //         //     'query' => $query->toSql(),
    //         //     'bindings' => $query->getBindings(),
    //         //     'request_data' => $request->all()
    //         // ]);
    //     } catch (\Exception $e) {
    //         \Log::error('Error in deepFilters method: ' . $e->getMessage(), [
    //             'stack' => $e->getTraceAsString(),
    //             'request_data' => $request->all()
    //         ]);
    //         // You can return an empty query or rethrow the exception
    //         return $query;
    //     }

    //     return $query;
    // }



    protected $fillable = [
        'user_id',
        'action',
        'action_timestamp',
        'object_name',
        'object_type',
        'balance_keeper',
        'location',
        'land_area',
        'building_area',
        'gas',
        'water',
        'electricity',
        'additional_info',
        'geolokatsiya',
        'latitude',
        'longitude',
        'kadastr_raqami',
        'sub_street_id',
        'street_id',
        'building_type',
        'kadastr_pdf',
        'hokim_qarori_pdf',
        'transfer_basis_pdf',
        'apartment_number',
        'home_number',

        'document_type', // Ҳужжат тури
        'reason_not_active', // Фаолият юритмаётганлиги сабаби
        'ready_for_rent', // Ижарага беришга тайёрлиги
        'rental_agreement_status', // Ижара шартномасини туздириш ҳолати
        'unused_duration', // Қанча вақтдан буён фойдаланилмайди
        'provided_assistance', // Берилган амалий ёрдам
        'start_date', // Фаолият юритишни бошлаган сана
        'additional_notes', // Изоҳ киритилган маълумотлардаги
        'working_24_7', // 24/7 режимда ишлайдими
        'stir', // СТИР

        'tenant_phone_number', // New column
        'ijara_summa_wanted', // New column
        'ijara_summa_fakt', // New column
    ];

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function substreet()
    {
        return $this->belongsTo(SubStreet::class, 'sub_street_id', 'id');
    }

    public function street()
    {
        return $this->belongsTo(Street::class, 'street_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
