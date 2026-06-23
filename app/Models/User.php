<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\UserProfile;
use App\Models\Dependent;
use App\Models\Payment;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'account_type',
        'linked_profile_id',
        'linked_dependent_id',
        'google_id',
        'avatar',
        'provider',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function dependents()
    {
        return $this->hasMany(Dependent::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function graveOrders()
    {
        return $this->hasMany(GraveOrder::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Jika user ini asalnya daripada dependent yang dinaikkan jadi Ahli Utama
    |--------------------------------------------------------------------------
    */
    public function linkedDependent()
    {
        return $this->belongsTo(Dependent::class, 'linked_dependent_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Jika user ini menggantikan Ahli Utama lama
    |--------------------------------------------------------------------------
    */
    public function replacingProfiles()
    {
        return $this->hasMany(UserProfile::class, 'replaced_by_user_id');
    }
}