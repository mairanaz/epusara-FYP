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
        'jantina',
        'agama',
        'alamat_rumah',
        'no_tel_bimbit',
        'tinggal_dalam_kariah',
        'tempoh_menetap',
        'pekerjaan',
        'nama_majikan',
        'alamat_kerja',
        'tarikh_permohonan',
        'status_permohonan',
        'catatan_permohonan',
        'payment_plan',
        'status_kehidupan',
        'tarikh_kematian',
        'replaced_by_user_id',
        'replaced_at',
        'replacement_reason',
        'replacement_dependent_id',
        'replacement_status',
    ];

    protected $casts = [
        'tarikh_lahir' => 'date',
        'tarikh_permohonan' => 'date',
        'tarikh_kematian' => 'date',
        'tinggal_dalam_kariah' => 'boolean',
        'replaced_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

     /*
    |--------------------------------------------------------------------------
    | User anak yang menggantikan Ahli Utama lama
    |--------------------------------------------------------------------------
    */
    public function replacedByUser()
    {
        return $this->belongsTo(User::class, 'replaced_by_user_id');
    }
}