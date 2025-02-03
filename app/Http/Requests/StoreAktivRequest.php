<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAktivRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Adjust based on your authorization logic
    }

    public function rules()
    {
        return [
            'object_name'      => 'required',
            'balance_keeper'   => 'required',
            'location'         => 'required',
            'land_area'        => 'required|numeric',
            'building_area'    => 'nullable',
            'gas'              => 'required|string',
            'water'            => 'required|string',
            'electricity'      => 'required|string',
            'additional_info'  => 'nullable',
            'geolokatsiya'     => 'required|string',
            'latitude'         => 'required|numeric',
            'longitude'        => 'required|numeric',
            'kadastr_raqami'   => 'nullable',
            'files.*'          => 'required',
            'files' => 'required|array|min:4', // Enforces at least 4 files

            'sub_street_id'    => 'required',
            'street_id'    => 'required',
            'home_number'          => 'nullable',
            'apartment_number'          => 'nullable',

            'user_id'          => 'nullable',
            'building_type' => 'nullable|in:yer,kopQavatliUy,AlohidaSavdoDokoni',

            'kadastr_pdf'      => 'nullable|file',
            'ijara_shartnoma_nusxasi_pdf' => 'nullable|file',
            'qoshimcha_fayllar_pdf' => 'nullable|file',

            'document_type' => 'nullable',
            'reason_not_active' => 'nullable',
            'ready_for_rent' => 'nullable',
            'rental_agreement_status' => 'nullable',
            'unused_duration' => 'nullable',
            'provided_assistance' => 'nullable',
            'start_date' => 'nullable',
            'additional_notes' => 'nullable',
            'working_24_7' => 'nullable',
            'owner' => 'nullable',
            'stir' => 'nullable',
            'object_type' => 'nullable',
            'tenant_phone_number' => 'nullable',
            'ijara_summa_wanted' => 'nullable',
            'ijara_summa_fakt' => 'nullable',
            'ijaraga_berishga_tayyorligi' => 'nullable',
            'faoliyat_xolati' => 'nullable',
        ];
    }
}
