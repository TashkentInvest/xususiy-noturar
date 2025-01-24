<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubStreet extends Model
{
    use HasFactory;

    protected $table = 'sub_streets';
    protected $fillable = ['name', 'name_ru', 'type', 'comment', 'code', 'district_id','street_id'];

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function street()
    {
        return $this->belongsTo(Street::class, 'street_id');
    }
}
