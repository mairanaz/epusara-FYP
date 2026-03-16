<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'payment_plan',
        'payment_type',
        'amount',
        'membership_year',
        'paid_month',
        'payment_period',
        'status',
        'paid_at',
        'payment_method',
        'reference_no',
        'receipt_no',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}