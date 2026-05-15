<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'payment_type',
        'amount',
        'membership_year',
        'paid_month',
        'billing_month',
        'cycle_start',
        'cycle_end',
        'payment_period',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'billing_month' => 'date',
        'cycle_start' => 'date',
        'cycle_end' => 'date',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}