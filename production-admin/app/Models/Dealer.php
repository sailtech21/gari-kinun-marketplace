<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dealer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'address',
        'business_name',
        'business_address',
        'business_phone',
        'business_license',
        'nid_front',
        'nid_back',
        'selfie_photo',
        'verification_code',
        'mobile_verified_at',
        'is_verified',
        'is_featured',
        'is_suspended',
        'status',
        'applied_at',
        'approved_at',
        'badge',
        'subscription_tier',
        'subscription_starts_at',
        'subscription_ends_at',
        'subscription_price',
        'listing_limit',
        'total_revenue',
    ];

    protected $casts = [
        'applied_at' => 'datetime',
        'approved_at' => 'datetime',
        'mobile_verified_at' => 'datetime',
        'is_verified' => 'boolean',
        'is_featured' => 'boolean',
        'is_suspended' => 'boolean',
        'subscription_starts_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'subscription_price' => 'decimal:2',
        'total_revenue' => 'decimal:2',
        'listing_limit' => 'integer',
    ];

    /**
     * Check if subscription is active
     */
    public function hasActiveSubscription()
    {
        return $this->subscription_ends_at && $this->subscription_ends_at->isFuture();
    }

    /**
     * Get remaining listing slots
     */
    public function getRemainingListingsAttribute()
    {
        return $this->listing_limit - $this->listings()->count();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }
}