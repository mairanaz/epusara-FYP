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
        'no_tel',
        'status_kehidupan',
        'tarikh_kematian',
    ];

    protected $casts = [
        'tarikh_kematian' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}