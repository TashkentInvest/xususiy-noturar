<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyManagement extends Model
{
    use HasFactory;

    protected $table = 'company_managements';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'user_id',
        'district',
        'address',
        'inn',
        'organization',
        'representative',
        'phone',
        'service_phone',
    ];

    public function street()
    {
        return $this->belongsTo(Street::class);
    }

    public function aktiv()
    {
        return $this->hasMany(Aktiv::class);
    }
}
