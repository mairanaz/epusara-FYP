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
        'promoted_user_id',
        'promoted_at',
    ];

    protected $casts = [
        'tinggal_bersama' => 'boolean',
        'tarikh_kematian' => 'date',
        'tarikh_keluar_tanggungan' => 'date',
        'promoted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /*
    |--------------------------------------------------------------------------
    | Akaun user yang dicipta apabila dependent ini dinaikkan jadi Ahli Utama
    |--------------------------------------------------------------------------
    */
    public function promotedUser()
    {
        return $this->belongsTo(User::class, 'promoted_user_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Semak jika dependent ini sudah ada akaun user melalui linked_dependent_id
    |--------------------------------------------------------------------------
    */
    public function linkedUser()
    {
        return $this->hasOne(User::class, 'linked_dependent_id');
    }

    public function deathReports()
    {
        return $this->hasMany(\App\Models\DeathReport::class, 'dependent_id');
    }
}