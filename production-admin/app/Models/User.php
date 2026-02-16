<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'firebase_uid',
        'name',
        'email',
        'password',
        'phone',
        'address',
        'avatar',
        'role',
        'status',
        'is_premium',
        'can_post',
        'listing_limit',
        'role_id',
        'favorites',
        'notification_settings',
        'google_id',
        'phone_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_premium' => 'boolean',
        'can_post' => 'boolean',
        'favorites' => 'array',
        'notification_settings' => 'array',
    ];

    public function listings()
    {
        return $this->hasMany(\App\Models\Listing::class);
    }
    
    public function reports()
    {
        return $this->hasMany(\App\Models\Report::class, 'reported_by');
    }
    
    public function dealer()
    {
        return $this->hasOne(\App\Models\Dealer::class);
    }
    
    public function adminRole()
    {
        return $this->belongsTo(\App\Models\AdminRole::class, 'role_id');
    }
    
    public function isVerifiedDealer()
    {
        return $this->dealer && $this->dealer->status === 'active' && $this->dealer->is_verified;
    }
}
