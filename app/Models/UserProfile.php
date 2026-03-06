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
    'email',
    'tarikh',
    'alamat_rumah',
    'no_tel_rumah',
    'no_tel',
    'pekerjaan',
    'alamat_kerja',
];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}