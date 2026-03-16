<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama',
        'no_kp',
        'tarikh_lahir',
        'agama',
        'warganegara',
        'alamat_rumah',
        'no_tel_rumah',
        'no_tel_bimbit',
        'tinggal_dalam_kariah',
        'tempoh_menetap',
        'pekerjaan',
        'nama_majikan',
        'alamat_kerja',
        'nama_waris',
        'hubungan_waris',
        'no_tel_waris',
        'alamat_waris',
        'tarikh_permohonan',
        'status_permohonan',
        'catatan_permohonan',
        'payment_plan',
    ];

    protected $casts = [
        'tarikh_lahir' => 'date',
        'tarikh_permohonan' => 'date',
        'tinggal_dalam_kariah' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}