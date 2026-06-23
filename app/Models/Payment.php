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
        'original_user_id',
        'transferred_from_user_id',
        'transferred_to_user_id',
        'transferred_at',
        'transfer_reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'billplz_paid' => 'boolean',
        'billplz_data' => 'array',
        'transferred_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(PaymentItem::class);
    }

    public function originalUser()
    {
        return $this->belongsTo(User::class, 'original_user_id');
    }

    public function transferredFromUser()
    {
        return $this->belongsTo(User::class, 'transferred_from_user_id');
    }

    public function transferredToUser()
    {
        return $this->belongsTo(User::class, 'transferred_to_user_id');
    }

    
}