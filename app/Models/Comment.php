<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'aktiv_id', 'content'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function aktiv()
    {
        return $this->belongsTo(Aktiv::class);
    }
}
