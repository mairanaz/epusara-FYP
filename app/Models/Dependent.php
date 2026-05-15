<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dependent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'no_kp',
        'pasangan',
        'pertalian',
        'status_perkahwinan',
        'tinggal_bersama',
        'status_tanggungan',
        'sebab_tidak_layak',
        'tarikh_keluar_tanggungan',
        'no_tel',
        'status_kehidupan',
        'tarikh_kematian',
    ];

    protected $casts = [
        'tinggal_bersama' => 'boolean',
        'tarikh_kematian' => 'date',
        'tarikh_keluar_tanggungan' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}