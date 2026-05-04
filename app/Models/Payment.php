<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PaymentItem;

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
        'billplz_bill_id',
        'billplz_url',
        'billplz_paid',
        'billplz_state',
        'billplz_data',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'billplz_paid' => 'boolean',
        'billplz_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(PaymentItem::class);
    }

    
}