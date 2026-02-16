<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image',
        'link',
        'button_text',
        'order',
        'priority',
        'is_active',
        'position',
        'advertiser_name',
        'advertiser_email',
        'advertiser_phone',
        'monthly_price',
        'starts_at',
        'ends_at',
        'scheduled_start',
        'scheduled_end',
        'is_paid',
        'clicks',
        'impressions',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_paid' => 'boolean',
        'order' => 'integer',
        'priority' => 'integer',
        'clicks' => 'integer',
        'impressions' => 'integer',
        'monthly_price' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'scheduled_start' => 'datetime',
        'scheduled_end' => 'datetime',
    ];
}
